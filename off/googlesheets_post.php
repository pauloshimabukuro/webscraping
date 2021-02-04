<?php
    $sheets_url = 'https://script.google.com/macros/s/AKfycbxClYcN6LB8F-5fejBcrnr2712doPI6RrTmhFuDw3zXtroa0qJP/exec';

    # Our new data
    $data = array(
        'data_pesquisa'    => date('Y-m-d'),
        'nome_produto' => 'Shampoo a seco Charming',
        'preco_da_loja'   => 35,
        'produtos_retornados' => 20,
        'produtos_acima_do_preco' => 17,
        'produtos_abaixo_do_preco' => 3,
        'preco_medio' => 37,
        'taxa_de_compatitividade' => '85%',
        'preco_comparado_com_a_media' => -2
    );

    # Create a connection
    $url = $sheets_url;
    $ch = curl_init($url);

    # Form data string
    $postString = http_build_query($data, '', '&');

    # Setting our options
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    # Get the response
    $response = curl_exec($ch);
    curl_close($ch);

?>