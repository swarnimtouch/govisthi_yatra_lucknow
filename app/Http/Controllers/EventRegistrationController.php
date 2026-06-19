<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Typography\FontFactory;

class EventRegistrationController extends Controller
{
    const PHOTO_X      = 654;
    const PHOTO_Y      = 1061;
    const PHOTO_WIDTH  = 180;
    const PHOTO_HEIGHT = 215;

    const NAME_X       = 870;
    const NAME_Y       = 1224;
    const NAME_MAX_WIDTH = 420;   // banner par name ka max pixel width
    const NAME_MIN_SIZE  = 16;
    const NAME_SIZE    = 35;

    public function index()
    {
        $cities = EventRegistration::cityData();
        return view('events.index', compact('cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'     => 'required|string|max:100',
            'gender'        => 'required|in:male,female',
            'mobile'        => 'nullable|string|max:15',
            'email'         => 'nullable|email|max:191',
            'city'          => 'required|string',
            'cropped_photo' => 'required|string',
        ]);

        $cities = EventRegistration::cityData();

        if (!isset($cities[$request->city])) {
            return back()->withErrors([
                'city' => 'Invalid city selected.'
            ]);
        }

        $cityInfo = $cities[$request->city];

        /*
        |--------------------------------------------------------------------------
        | Upload Cropped Photo To S3
        |--------------------------------------------------------------------------
        */
        $base64 = preg_replace(
            '/^data:image\/\w+;base64,/',
            '',
            $request->cropped_photo
        );

        $imageData = base64_decode($base64);
        $fullName = preg_replace('/[^A-Za-z0-9_-]/', '_', $request->full_name);

        $croppedFilename = 'cropped_'. $fullName .'.jpg';
        $croppedPath     = 'govisthi_yatra/cropped/' . $croppedFilename;

        Storage::disk('s3')->put(
            $croppedPath,
            $imageData,
            [
                'visibility'  => 'public',
                'ContentType' => 'image/jpeg',
            ]
        );

        $bannerOutput = null;


        $bannerKey  = $request->gender === 'female' ? 'banner_f' : 'banner';
        $bannerFile = public_path('banners/' . ($cityInfo[$bannerKey] ?? $cityInfo['banner']));


        if (file_exists($bannerFile)) {

            $manager = new ImageManager(new Driver());

            $banner = $manager->read($bannerFile);

            $photoContents = Storage::disk('s3')->get($croppedPath);

            $photo = $manager->read($photoContents);

            $photo->cover(
                self::PHOTO_WIDTH,
                self::PHOTO_HEIGHT
            );

            $banner->place(
                $photo,
                'top-left',
                self::PHOTO_X,
                self::PHOTO_Y
            );

            $fontPath = public_path('fonts/AnekDevanagari-Bold.ttf');

            if (file_exists($fontPath)) {

                // ─── Auto font size ───────────────────────────────────────
                $fontSize = self::NAME_SIZE; // 35 se shuru

                while ($fontSize > self::NAME_MIN_SIZE) {
                    // imagettfbbox: GD se text ka bounding box milta hai
                    $bbox      = imagettfbbox($fontSize, 0, $fontPath, $request->full_name);
                    $textWidth = abs($bbox[4] - $bbox[0]); // actual pixel width

                    if ($textWidth <= self::NAME_MAX_WIDTH) {
                        break; // fit ho gaya, loop band
                    }

                    $fontSize--; // ek ek pixel kam karo
                }
                // ─────────────────────────────────────────────────────────

                $banner->text(
                    $request->full_name,
                    self::NAME_X,
                    self::NAME_Y,
                    function (FontFactory $font) use ($fontPath, $fontSize) {  // $fontSize pass karo
                        $font->file($fontPath);
                        $font->size($fontSize);   // dynamic size
                        $font->color('#FFFFFF');
                        $font->align('left');
                        $font->valign('top');
                    }
                );
            }



            $bannerFilename = 'banner_'. $fullName .'.jpg';

            $bannerOutput   = 'govisthi_yatra/banners/' . $bannerFilename;

            $encodedBanner = $banner->toJpeg(90);

            Storage::disk('s3')->put(
                $bannerOutput,
                $encodedBanner->toString(),
                [
                    'visibility'  => 'public',
                    'ContentType' => 'image/jpeg',
                ]
            );
        }


        $registration = EventRegistration::create([
            'full_name'        => $request->full_name,
            'gender'           => $request->gender,
            'mobile'           => $request->mobile,
            'email'            => $request->email,
            'city'             => $request->city,

            'event_date' => \Carbon\Carbon::createFromFormat(
                'j F Y',
                $cityInfo['date'] . ' ' . date('Y')
            )->format('Y-m-d'),

            'photo_cropped'    => $croppedPath,
            'generated_banner' => $bannerOutput,
        ]);

        return redirect()->route('event.result', $registration->id);
    }

    public function result($id)
    {
        $reg = EventRegistration::findOrFail($id);

        $reg->banner_url = $reg->generated_banner
            ? Storage::disk('s3')->url($reg->generated_banner)
            : null;

        $reg->photo_url = $reg->photo_cropped
            ? Storage::disk('s3')->url($reg->photo_cropped)
            : null;

        return view('events.result', compact('reg'));
    }

    public function downloadBanner($id)
    {
        $registration = EventRegistration::findOrFail($id);

        if (!$registration->generated_banner) {
            abort(404);
        }

        $file = Storage::disk('s3')->get($registration->generated_banner);

        return response($file)
            ->header('Content-Type', 'image/jpeg')
            ->header(
                'Content-Disposition',
                'attachment; filename="banner-'.$registration->id.'.jpg"'
            );
    }
}
