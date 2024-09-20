<?php

namespace App\Console\Commands;

use App\Models\scrape;
use Goutte\Client;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class ScrapeSite extends Command
{
    protected $signature = 'scrape:site {produto}';
    protected $description = 'Buscar produtos no marketplace';

    public function handle()
    {
        $produto = $this->argument('produto');
        $produtoFormatado = str_replace(' ', '-', $produto);
        
        $url = "https://lista.mercadolivre.com.br/$produtoFormatado/";

        $client = new Client();
        $crawler = $client->request('GET', $url, [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
        ]);
        $items = $crawler->filter('li.ui-search-layout__item');
        
        foreach ($items as $node) {
            $crawlerNode = new Crawler($node);
            try{
                if ($crawlerNode->filter('.poly-card__portada img.poly-component__picture')->count() > 0) {
                    $image = $crawlerNode->filter('.poly-card__portada img.poly-component__picture')->attr('data-src');
        
                    if (!$image) {
                        $image = $crawlerNode->filter('.poly-card__portada img.poly-component__picture')->attr('src');
                    }

                    if (strpos($image, 'data:image') === false) {
                        $name = $crawlerNode->filter('.poly-component__title a')->text();
                        $currentPrice = $crawlerNode->filter('.poly-price__current span.andes-money-amount__fraction')->text();
        
                        echo "Imagem: $image\n";
                        echo "Nome: $name\n";
                        echo "Preço: R$ $currentPrice\n";
                        echo "------------------------\n";
                        scrape::create([
                            'product_search' => $produto,
                            'image_url' => $image,
                            'price' => $currentPrice,
                            'name' => $name,
                        ]);
                    }
                }
            }catch(\Symfony\Component\HttpClient\Exception\InvalidArgumentException $e){
              echo "erro ao passar argumento invalido!";
            }catch(\Throwable $e){
                dd($e);
            }
        }
    }
}
