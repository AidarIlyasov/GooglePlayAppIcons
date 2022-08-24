<?php

namespace App\Http\Controllers;

use App\Exceptions\BadAppNameException;
use Illuminate\Http\Request;
use FastSimpleHTMLDom\Document;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    private $baseUrl = 'https://play.google.com/store/apps/details?id=';

    public function show(Request $request)
    {
        $appName = $request->query('app');
        if (is_null($appName)) return response('false');

        $imageUrl = $this->getIconUrlFromGooglePlay($appName);

        $this->saveFileToStorage($imageUrl, $appName);

        return response()->file(Storage::path($appName . '.jpg'), [
            'Content-header' => 'application/jpg'
        ]);
    }

    public function display(Request $request)
    {
        $appName = $request->input('app');
        if (is_null($appName)) return response('false');

        return [
            'app_icon' => $this->getIconUrlFromGooglePlay($appName),
        ];
    }

    /**
     * @throws BadAppNameException
     */
    private function getIconUrlFromGooglePlay(string $appName): string
    {
        try {
            $html = new Document(file_get_contents($this->baseUrl . $appName));
        } catch (\ErrorException $exception) {
            throw new BadAppNameException();
        }

        return $html->find('img.arM4bb', 0)->src;
    }

    private function saveFileToStorage(string $imageUrl, string $imageName): void
    {
        Storage::put($imageName . '.jpg', file_get_contents($imageUrl));
    }
}
