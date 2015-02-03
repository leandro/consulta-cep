<?php
  // It uses nokogiri library from: https://github.com/olamedia/nokogiri
  require 'nokogiri.php';

  class CEPData {
    private static $urlBase = 'http://m.correios.com.br/movel/buscaCepConfirma.do';

    private static function getQueryString($cep) {
      return 'cepEntrada=' . $cep . '&tipoCep=&cepTemp=&metodo=buscarCep';
    }

    public static function normalizeCEP($cep) {
      if(empty($cep) || !isset($cep)) { return false; }

      if(strlen($cep) > 8 ):
        $cep = preg_replace('/[^0-9]/', '', $cep);
      endif;

      return $cep;
    }

    public static function fetch($cep) {
      $cep = self::normalizeCEP($cep);

      if(strlen($cep) != 8) return false;

      $ch = curl_init(self::$urlBase . '?');
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, self::getQueryString($cep));
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $html = curl_exec($ch);
      curl_close($ch);

      $dom = new nokogiri($html);
      $address_parts = Array();

      foreach($dom->get('.respostadestaque') as $item):
        $address_part = $item['#text'][0];
        array_push($address_parts, preg_replace('/\s{2,}/', '', trim($address_part)));
      endforeach;

      return implode(' - ', $address_parts) . "\n";
    }
  }
?>
