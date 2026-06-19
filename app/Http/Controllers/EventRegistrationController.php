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
    const DEFAULT_CITY = 'Lucknow (28 July)';

    const PHOTO_X      = 518;
    const PHOTO_Y      = 485;
    const PHOTO_WIDTH  = 141;
    const PHOTO_HEIGHT = 175;

    const NAME_X       = 590;
    const NAME_Y       = 675;
    const NAME_MAX_WIDTH = 129;
    const NAME_MIN_SIZE  = 11;
    const NAME_SIZE    = 35;

    public function index()
    {
        return view('events.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'     => 'required|string|max:100',
            'gender'        => 'required|in:male,female',
            'mobile'        => 'nullable|string|max:15',
            'email'         => 'nullable|email|max:191',
            'cropped_photo' => 'required|string',
        ]);

        $cities = EventRegistration::cityData();

        if (!isset($cities[self::DEFAULT_CITY])) {
            return back()->withErrors([
                'city' => 'Invalid city configuration. Check DEFAULT_CITY constant.'
            ]);
        }

        $cityInfo = $cities[self::DEFAULT_CITY];

        $base64 = preg_replace(
            '/^data:image\/\w+;base64,/',
            '',
            $request->cropped_photo
        );

        $imageData = base64_decode($base64);
        $fullName = preg_replace('/[^A-Za-z0-9_-]/', '_', $request->full_name);

        $croppedFilename = 'cropped_'. $fullName .'.jpg';
        $croppedPath     = 'govisthi_yatra_lucknow/cropped/' . $croppedFilename;

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

                $fontSize = self::NAME_SIZE;

                while ($fontSize > self::NAME_MIN_SIZE) {
                    $bbox      = imagettfbbox($fontSize, 0, $fontPath, $request->full_name);
                    $textWidth = abs($bbox[4] - $bbox[0]);

                    if ($textWidth <= self::NAME_MAX_WIDTH) {
                        break;
                    }

                    $fontSize--;
                }

                $banner->text(
                    $request->full_name,
                    self::NAME_X,
                    self::NAME_Y,
                    function (FontFactory $font) use ($fontPath, $fontSize) {
                        $font->file($fontPath);
                        $font->size($fontSize);
                        $font->color('#000000');
                        $font->align('center');
                        $font->valign('top');
                    }
                );
            }

            $bannerFilename = 'banner_'. $fullName .'.jpg';

            $bannerOutput   = 'govisthi_yatra_lucknow/banners/' . $bannerFilename;

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
            'city'             => self::DEFAULT_CITY,

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
