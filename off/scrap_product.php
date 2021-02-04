<?php
    include('php-dom-parser/simple_html_dom.php');
    include('vendor/google_sheets_data_post.php');
    include('vendor/google_shop_scrap.php');

    #Define o array para serenviado a funcao de scrap
    $loja = array (
        'nome_produto' => 'Shampoo Joico K-Pak To Repair Damage 300ml para Cabelos Danificados',
        'preco_da_loja' => '109.90'
    );

    #Chama a funcao scrap
    $resultado = googleShopScrap($loja);

    #Envia os dados para o Google Sheets
    postToSheets($resultado);

    //print_r($resultado);

?>