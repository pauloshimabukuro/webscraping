<?php
    include('vendor/simple_html_dom.php');

    //Link do produto a venda na loja do cliente https://www.najucosmeticos.com.br/agua-auto-brozeadora-dark
    $codigoEanProduto = '7898965429072';
    $idProduto = '4255';
    $precoProduto = '110.50';
    $nome_produto = 'Água Autobronzeador Dark';
    $lojaProduto = 'Naju Cosméticos';
    $html = file_get_html('https://www.google.com/search?tbm=shop&q='.$codigoEanProduto);

    //Preco do produto
    $preco_da_loja = $precoProduto;

    //Remove caracteres especiais
    $remover = array("R", "$", ",");
    $colocar = array("", "", ".");

    //Define as variaves
    $preco_total = 0;
    $produtos_retornados = 0;
    $produtos_abaixo_do_preco = 0;
    $produtos_acima_do_preco = 0;

    //produto da loja do cliente
    echo '<h1>Pesquisa:</h1>';
    echo $lojaProduto;
    echo '<br>';
    echo $precoProduto;
    echo '<br>';
    echo '<br>';

    //Faz a leitura do google shopping
    foreach($html->find('a') as $element){
        $link = explode('/',parse_url($element->href, PHP_URL_PATH))[1];
        
        if($link == 'shopping'){
            $codigoGoogleProduto = explode('/',parse_url($element->href, PHP_URL_PATH))[3];
            $html = file_get_html('https://www.google.com/shopping/product/'.$codigoGoogleProduto.'?q='.$codigoEanProduto);
            //Resultado da busca
            foreach($html->find('div[class=t9KcM]') as $element2){                
                echo $element2->children(1)->children(0)->plaintext.'<br>';                
                $preco_num  = substr(str_replace($remover, $colocar, $element2->children(0)->children(0)->children(0)->plaintext),2);
                $preco_num = utf8_decode($preco_num);
                echo $preco_num.'<br><br>';

                $preco_total += $preco_num;
                $produtos_retornados += 1;

                if($preco_num > $preco_da_loja){
                    $produtos_acima_do_preco += 1;
                }else{
                    $produtos_abaixo_do_preco += 1;
                }
            }
        }        
    }

    //Define as variaveis calculadas
    $preco_medio = $preco_total / $produtos_retornados;
    $preco_comparado_com_a_media = $preco_da_loja - $preco_medio;
    $taxa_de_compatitividade = (100 / $produtos_retornados) * $produtos_acima_do_preco;

    //Envia as informações para o array
    $resultado_do_scrap =  array(
        'data_pesquisa'    => date('Y-m-d'),
        'id_produto' => $idProduto,
        'nome_produto' => $nome_produto,
        'preco_da_loja'   => number_format($preco_da_loja, 2, ',', ' '),
        'produtos_retornados' => $produtos_retornados,
        'produtos_acima_do_preco' => $produtos_acima_do_preco,
        'produtos_abaixo_do_preco' => $produtos_abaixo_do_preco,
        'preco_medio' => number_format($preco_medio, 2, ',', ' '),
        'taxa_de_compatitividade' => number_format($taxa_de_compatitividade, 2, '.', ' ').'%',
        'preco_comparado_com_a_media' => number_format($preco_comparado_com_a_media, 2, ',', ' ')
    );

    echo '<h1>Resultado:</h1>';

    foreach($resultado_do_scrap as $key => $value){
        echo $key.': '.$value; 
        echo '<br>';
    }


    //DAVID, DAQUI PRA BAIXO SÃO SÓ CODIGOS QUE EU ESTAVA TESTANDO, NÃO CHEGUEI A USAR MAS TEM UMA LOGICA DO PQ ESTAVA TESTANDO, SE QUISER TE PASSO TB CASO PRECISE.


    /*
    //Fução para puxar os dados do Google Shop
    function googleCustomSearch($data){
            #Google Sheets Script to receive data
            $url = 'https://www.googleapis.com/customsearch/v1?key=AIzaSyBaukuVS1u2apKatBU6V4JI4TJH06vdUdQ&cx=c207455749e1e9ab5&q='.$data['ean'];

            $ch = curl_init();
            // Set URL and header options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
            
            // Start to capture the output
            ob_start();

            // Excecuting the curl call
            curl_exec($ch);

            // Get the srtout content and clean the buffer
            $content = ob_get_clean();
            
            // Close the Curl resource
            curl_close($ch);	
            
            return $content;
    }
    */

    /*
    //Funcao que faz a leitura do XML google da loja
    function loadXML($link){

        #carrega o arquivo XML e retornando um Array
        $xml = simplexml_load_file($link);

        $array_produtos = array();
        
        #Faz um loop para ler  cada "item" e exibir as infromações de "title" e "preço"
        foreach($xml->channel->item as $item){
            //echo $item->title."<br>";
            $preco = $item->children('g', true)->price;
            $remover = array("R", "$", ",");
            $colocar = array("", "", ".");
            $preco_num  = str_replace($remover, $colocar, $preco);

            $nome_produto = $item->title->__toString();

            $id_produto = $item->children('g', true)->id->__toString();

            #Define o array para serenviado a funcao de scrap
            array_push($array_produtos, array('id_produto' => $id_produto, 'nome_produto' => $nome_produto, 'preco_da_loja' => $preco_num));
        }

        // encode array to json
        $json = json_encode($array_produtos);
        $bytes = file_put_contents("temp/produtos.json", $json); 
        echo "O arquivo tem o tamaho de $bytes.";

    }
    */

    /*
    //Funcao para ler arquivo json gravado na pasta 'temp'
    function readJson(){
        $result = file_get_contents("temp/produtos.json");
        $array = json_decode($result, true);

        foreach($array as $produto){

            $loja = array (
                    'id_produto' => $produto['id_produto'],
                    'nome_produto' => $produto['nome_produto'],
                    'preco_da_loja' => $produto['preco_da_loja']
            );   

            #Chama a funcao scrap
            $resultado = googleShopScrap($loja);

            #Envia os dados para o Google Sheets
            postToSheets($resultado);
        }   

    }
    */

    /*
    //Funcao para puxar os dados do produto no google shopping
    function googleShopScrap($data){

        //Dados do produto a ser buscado
        $nome_produto = $data['ean'];
        $preco_da_loja = $data['preco_da_loja'];

        //Link para scrap
        $html = file_get_html('https://www.google.com/search?sa=X&tbm=shop&q='.urlencode($nome_produto));  

        //Determina os elemtnos para buscar
        $preco = $html->find('span[class="HRLxBb"]');
        $preco = $html->find('div[class="qptdjc"]');
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

        //return (print_r($preco));

        //Define as variaveis calculadas
        $preco_medio = $preco_total / $produtos_retornados;
        $preco_comparado_com_a_media = $preco_da_loja - $preco_medio;
        $taxa_de_compatitividade = (100 / $produtos_retornados) * $produtos_acima_do_preco;

        //Envia as informações para o array
        $resultado_do_scrap =  array(
            'data_pesquisa'    => date('Y-m-d'),
            'id_produto' => $data['id_produto'],
            'nome_produto' => $nome_produto,
            'preco_da_loja'   => number_format($preco_da_loja, 2, ',', ' '),
            'produtos_retornados' => $produtos_retornados,
            'produtos_acima_do_preco' => $produtos_acima_do_preco,
            'produtos_abaixo_do_preco' => $produtos_abaixo_do_preco,
            'preco_medio' => number_format($preco_medio, 2, ',', ' '),
            'taxa_de_compatitividade' => number_format($taxa_de_compatitividade, 2, '.', ' ').'%',
            'preco_comparado_com_a_media' => number_format($preco_comparado_com_a_media, 2, ',', ' ')
        );

        return $resultado_do_scrap;
    }
    */

    /*
    //funcao que envia os dados para o Google Sheets
    function postToSheets($data){
        #Google Sheets Script to receive data
        $sheets_url = 'https://script.google.com/macros/s/AKfycbxClYcN6LB8F-5fejBcrnr2712doPI6RrTmhFuDw3zXtroa0qJP/exec';

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
    }
    */

    /*
    //funcao que puxa os dados para o Google Sheets
    function getFromSheets(){
        #Google Sheets Script to receive data
        $url = 'https://script.google.com/macros/s/AKfycbxClYcN6LB8F-5fejBcrnr2712doPI6RrTmhFuDw3zXtroa0qJP/exec';

         $ch = curl_init();
        // Set URL and header options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        
        // Start to capture the output
        ob_start();

        // Excecuting the curl call
        curl_exec($ch);

        // Get the srtout content and clean the buffer
        $content = ob_get_clean();
        
        // Close the Curl resource
        curl_close($ch);	
        
        return $content;
    } 
    */

?>