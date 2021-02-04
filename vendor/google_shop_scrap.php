<?php
    include('simple_html_dom.php');

    //Funcao para puxar os dados do produto no google shopping
    function googleShopScrap($data){

        //Dados do produto a ser buscado
        $nome_produto = $data['nome_produto'];
        $preco_da_loja = $data['preco_da_loja'];

        //Link para scrap
        $html = file_get_html('https://www.google.com/search?sa=X&tbm=shop&q='.urlencode($nome_produto));  

        //Determina os elemtnos para buscar
        $preco = $html->find('span[class="HRLxBb"]');
        $remover = array("R", "$", ",");
        $colocar = array("", "", ".");

        //Define as variaves vazias
        $preco_total = 0;
        $produtos_retornados = 0;
        $produtos_abaixo_do_preco = 0;
        $produtos_acima_do_preco = 0;

        foreach($preco as $element){
            $preco_num  = substr(str_replace($remover, $colocar, $element->innertext),1);
            $preco_total += $preco_num;
            $produtos_retornados += 1;
            if($preco_num > $preco_da_loja){
                $produtos_acima_do_preco += 1;
            }else{
                $produtos_abaixo_do_preco += 1;
            }
        }

        //Define as variaveis calculadas
        $preco_medio = $preco_total / $produtos_retornados;
        $preco_comparado_com_a_media = $preco_da_loja - $preco_medio;
        $taxa_de_compatitividade = (100 / $produtos_retornados) * $produtos_acima_do_preco;

        //Envia as informações para o array
        $resultado_do_scrap =  array(
            'data_pesquisa'    => date('Y-m-d'),
            'nome_produto' => $data['nome_produto'],
            'preco_da_loja'   => number_format($preco_da_loja, 2, ',', ' '),
            'produtos_retornados' => $produtos_retornados,
            'produtos_acima_do_preco' => $produtos_acima_do_preco,
            'produtos_abaixo_do_preco' => $produtos_abaixo_do_preco,
            'preco_medio' => number_format($preco_medio, 2, ',', ' '),
            'taxa_de_compatitividade' => $taxa_de_compatitividade.'%',
            'preco_comparado_com_a_media' => number_format($preco_comparado_com_a_media, 2, ',', ' ')
        );

        return $resultado_do_scrap;
    }

?>