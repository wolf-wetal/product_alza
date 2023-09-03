<?php

namespace App\Service;

use Symfony\Component\Panther\Client;

require __DIR__ . '/../../vendor/autoload.php';


class AlzaParser
{
    public function parseProductInfo($url)
    {

        $client = Client::createFirefoxClient(null, [
            '--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.5845.96 Safari/537.36',
            '--window-size=1200,1100',
            '--headless',
            '--disable-gpu',
        ]);
        $crawler = $client->request('GET', $url);

        $name = $crawler->filter('#h1c > h1')->count() ? $crawler->filter('#h1c > h1')->text() : 'Название не найдено';
        $description = $crawler->filter('#detailText > div.nameextc > span')->count() ? $crawler->filter('#detailText > div.nameextc > span')->text() : 'Описание не найдено';
        $price = $crawler->filter('.price-box__price-text span')->count() ? $crawler->filter('.price-box__price-text span')->text() : 'Цена не найдена';
        $image = $crawler->filter('#detailPicture > div.galleryComponent > div > div > div.detailGallery-alz-1 > div.detailGallery-alz-2.detailGallery-alz-3 > div > swiper-container > swiper-slide.swiper-slide-active > div > img')->count() ? $crawler->filter('#detailPicture > div.galleryComponent > div > div > div.detailGallery-alz-1 > div.detailGallery-alz-2.detailGallery-alz-3 > div > swiper-container > swiper-slide.swiper-slide-active > div > img')->attr('src') : '';


        return ['name' => $name, 'description' => $description, 'price' => $price, 'image' => $image];
    }
}