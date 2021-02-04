<?php
    include('php-dom-parser/simple_html_dom.php');
    include('vendor/google_sheets_data_post.php');

    //Dados do produto a ser buscado
    $q = 'Shampoo Joico K-Pak To Repair Damage 300ml para Cabelos Danificados';
    $preco_loja = '109.90';

    $html = file_get_html('https://www.google.com/search?sa=X&tbm=shop&q='.urlencode($q));  
    
    echo 'Termo de busca:<br><b>'.$q.'</b><br><br>';

    $preco = $html->find('span[class="HRLxBb"]');
    $remover = array("R", "$", ",");
    $colocar = array("", "", ".");

    $preco_total = 0;
    $produto_total = 0;
    $preco_abaixo = 0;
    $preco_acima = 0;

    foreach($preco as $element){
        $preco_num  = substr(str_replace($remover, $colocar, $element->innertext),1);
        //echo 'R$ '.number_format($preco_num, 2, ',', ' ');
        //echo '<br>';
        $preco_total += $preco_num;
        $produto_total += 1;

        if($preco_num > $preco_loja){
            $preco_acima += 1;
            echo '<span style="color: red"><b>Preço acima: R$ '.number_format($preco_num, 2, ',', ' ').'</b></span><br>';
        }else{
            $preco_abaixo += 1;
            echo '<span style="color: green"><b>Preço abaixo: R$ '.number_format($preco_num, 2, ',', ' ').'</b></span><br>';
        }
    }

    $preco_medio = $preco_total / $produto_total;
    $preco_diferenca = $preco_loja - $preco_medio;
    $media_relacionado_marcado = (100 / $produto_total) * $preco_acima;

    echo '<br>';
    echo 'Produtos buscados: '.$produto_total;   
    echo '<br>';
    if($media_relacionado_marcado > 70){
        echo '<span style="color: green"><b>Sua loja tem o melhor preço em '.$media_relacionado_marcado.'% dos casos.</b></span>';
    }else{
        echo '<span style="color: red"><b>Sua loja tem o melhor preço em '.$media_relacionado_marcado.'% dos casos.</b></span>';
    }
    echo '<br>';
    echo '<br>';
    echo 'Preço médio: R$ '.number_format($preco_medio, 2, ',', ' ');
    echo '<br>';
    echo 'Preço da loja: R$ '.number_format($preco_loja, 2, ',', ' ');
    echo '<br>';
    echo '<br>';
    if($preco_medio > $preco_loja){
        echo '<span style="color: green"><b>Preço base comparado o preço médio: R$ '.number_format($preco_diferenca, 2, ',', ' ').'</b></span>';
    }else{
        echo '<span style="color: red"><b>Preço base comparado o preço médio: R$ '.number_format($preco_diferenca, 2, ',', ' ').'</b></span>';
    }


    //Envia as informações para o Google Sheets
    $informacoes =  array(
        'data_pesquisa'    => date('Y-m-d'),
        'nome_produto' => $q,
        'preco_da_loja'   => number_format($preco_loja, 2, ',', ' '),
        'produtos_retornados' => $produto_total,
        'produtos_acima_do_preco' => $preco_acima,
        'produtos_abaixo_do_preco' => $preco_abaixo,
        'preco_medio' => number_format($preco_medio, 2, ',', ' '),
        'taxa_de_compatitividade' => $media_relacionado_marcado.'%',
        'preco_comparado_com_a_media' => number_format($preco_diferenca, 2, ',', ' ')
    );
    postToSheets($informacoes);

?>