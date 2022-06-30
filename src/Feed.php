<?php

namespace RayNl\LaravelFacebookCatalog;

use Spatie\ArrayToXml\ArrayToXml;

class Feed{

	protected $xml 			= [];
	protected $products		= [];
	protected $title		= "";
	protected $description	= "";
	protected $link			= "";
	protected $currency 	= "EUR";

	public function __construct()
	{

	}

	public function setTitle($title = "")
	{
		$this->title = $title;
	}

	public function setDescription($description = "")
	{
		$this->description = $description;
	}

	public function setLink($link = "")
	{
		$this->link = $link;
	}
	
	 public function setCurrency($currency = 'EUR')
	{
		$this->currency = $currency;
	}

	public function addItem(
		$link 			= "",
		$id 			= "",
		$title 			= "",
		$image_link		= "",
		$description 	= "",
		$availability 	= "",
		$price 			= 0.00,
                $sale_price     = null,
		$brand 			= "",
		$gtin 			= "",
		$shipping		= [],
		// Optional
		$condition 		= "new",
		$product_type	= NULL,
		$custom_fields	= NULL
	){
		$product = [
			"g:id" 				=> $id,
			"g:title" 			=> $title,
            "g:description" 	=> strip_tags($description),
            "g:link" 			=> $link . "?utm_source=facebook&utm_medium=facebookCatalog&utm_campaign=",
            "g:image_link"		=> $image_link,
            "g:brand" 			=> $brand,
            "g:condition" 		=> $condition,
            "g:availability" 	=> $availability,
            "g:price" 			=> number_format($price, 2, ".", ",") . " " . $this->currency,
            "g:shipping"			=> [
				"g:country"			=> $shipping['country'],
				"g:service"			=> $shipping['service'],
				"g:price"				=> $shipping['price'],
			],
		];

        if (!is_null($sale_price)) {
            $product['g:sale_price'] = number_format($sale_price, 2, ".", ",") . " " . $this->currency;
        }

		if(!is_null($product_type)){
			$product['g:product_type'] = $product_type;
		}

		if(!is_null($custom_fields)){
			foreach ($custom_fields as $key => $value) {
				$product["g:" . $key] = $value;
			}
		}

		$this->products[] = $product;
	}

	public function generate()
	{
		$this->xml = [
		    'rss' 	=> [
		        '_attributes' 	=> [
		            'xmlns:g' 		=> 'http://base.google.com/ns/1.0',
		            'version' 		=> '2.0',
		        ],
		        'channel' 		=> [
		        	'title'			=> $this->title,
		        	'description'	=> $this->description,
		        	'link'			=> $this->link,
		        ]
		    ]
		];

		$i = 0;

		foreach ($this->products as $product) {
			$this->xml['rss']['channel']['item_'.$i] = $product;
			$i++;
		}

        $arrayToXml = new ArrayToXml($this->xml);
        $xml = $arrayToXml->prettify()->toXml();

        $xml = str_replace(['    ', '<root>', '</root>', '<remove>remove</remove>'], '', $xml);
		$xml = preg_replace([
			"/item_[0-9][0-9][0-9][0-9]/",
			"/item_[0-9][0-9][0-9]/",
			"/item_[0-9][0-9]/",
			"/item_[0-9]/",
		], "item", $xml);
		return $xml;

	}

	public function display()
	{
		return response($this->generate())->header('Content-Type', 'text/xml');
	}

}
