<?php

include_once "configs/funcoes.php";
include_once "Imagem.class.php";

class Noticias
{
	function Noticias()
    {
		$this->entidade = "noticias";

		$this->pathProjeto = PATH_SERVIDOR;
		
		$classImagem = new Imagem();
		$this->classImagem = $classImagem;
    }
	
	function Grava($post, $file, $file2)
	{

		$retorno = array();

		//Checa se existe imagem
		if ($file['name'] != "")
		{
			//Grava a Imagem
			$retornoManual = $this->classImagem->gravaImagem($file, $post['extencoeValidas'], "upload/noticias/", $copy);
			if( $retornoManual[0] )
			{
				$retorno[0] = "1";
				$retorno[1] = $retornoManual[1];
				return $retorno;
			}
		}

		//Checa se existe imagem
		if ($file2['name'] != "")
		{
			//Grava a Imagem
			$retornoClassImagem2 = $this->classImagem->gravaImagem($file2, $post['extencoeValidas'], "upload/manual/", $copy);
			if( $retornoClassImagem2[0] )
			{
				$retorno[0] = "1";
				$retorno[1] = $retornoClassImagem2[1];
				return $retorno;
			}
		}
	
		$sql = "
			INSERT INTO
				".$this->entidade."
			SET
				titulo			= '". $post['titulo'] ."',
				especial		= '". $post['especial'] ."',
				destaque		= '". $post['destaque'] ."',
				descricao		= '".utf8_decode($post['descricao'])."',
				manual			= '".$retornoClassImagem2[1]."',
				caminhoImagem	= '".$retornoManual[1]."'
		";

		$result = mysql_query($sql);
		if (!($result))
		{
			$retorno[0] = "1";
			$retorno[1] = "Erro ao executar a query. Classe = ".$this->entidade." - Metodo = grava";
			return $retorno;
		}
		else
		{
			//Descobri ID gerado
			$idProduto = mysql_insert_id();

			if($idProduto > 0)
			{
				for($i=0; $i < count($post['idCategoria']); $i++)
				{
					$parametroCategoria['idProduto'] = $idProduto;
					$parametroCategoria['idCategoria'] = $post['idCategoria'][$i];

					$retorno = $this->Grava_Produto_Categoria($parametroCategoria);
					if( $retorno[0] )
					{
						$retorno[0] = "1";
						$retorno[1] = "N�o foi poss�vel vincular o produto � categoria!";
						return $retorno;
					}
				}
			}

			$retorno[0] = 0;
			$retorno[1] = "Registro inserido com sucesso.";
			$retorno[2] = $idProduto;
			return $retorno;
		}
	}

	function Grava_Produto_Categoria($post)
	{

		$retorno = array();

		if($post['id'])
		{
			$query .= " AND produtocategoria.id = '".$post['id']."' ";
		}
		
		$sql = "
			INSERT INTO
				produtocategoria
			SET
				idProduto		= '".$post['idProduto']."',
				idCategoria		= '".$post['idCategoria']."'
		";
		$result = mysql_query($sql);
		if (!($result))
		{
			$retorno[0] = "1";
			$retorno[1] = "Erro ao executar a query. Classe = ".$this->entidade." - Metodo = Grava_Produto_Categoria";
			return $retorno;
		}
		else
		{
			$retorno[0] = 0;
			$retorno[1] = "Registro inserido com sucesso.";
			return $retorno;
		}
	}
	
	function Altera($post, $file, $file2)
	{
		$retorno = array();

		//Checa se ser� autializado a imagem
		if ($file['name'] != "")
		{
			if( $file["name"] != "" )
			{
				//Checa se existe a imagem
				if( file_exists($file["name"]) )
				{
					//Exclui a antiga
					if(!unlink( $this->pathProjeto.$file["name"] ))
					{
						$retorno[0] = "1";
						$retorno[1] = "N�o foi poss�el excluir a imagem antiga!";
						return $retorno;
					}
				}
			}
			
			//Grava a Imagem
			$retornoClassImagem = $this->classImagem->gravaImagem($file, $post['extencoeValidas'], "upload/produto/", $copy);
			if( $retornoClassImagem[0] )
			{
				$retorno[0] = "1";
				$retorno[1] = $retornoClassImagem[1];
				return $retorno;
			}
		}
		else
		{
			//Mant�m a imagem antiga
			$retornoClassImagem[1] = $post["caminhoImagem"];
		}


		//Checa se ser� autializado a imagem
		if ($file2['name'] != "")
		{
			if( $file2['name'] != "" )
			{
				//Checa se existe a imagem
				if( file_exists($file2['name']) )
				{
					//Exclui a antiga
					if(!unlink( $this->pathProjeto.$file2['name'] ))
					{
						$retorno[0] = "1";
						$retorno[1] = "N�o foi poss�el excluir a imagem antiga 2!";
						return $retorno;
					}
				}
			}
			
			//Grava a Imagem
			$retornoManual = $this->classImagem->gravaImagem($file2, $post['extencoeValidas'], "upload/manual/", $copy);
			if( $retornoManual[0] )
			{
				$retorno[0] = "1";
				$retorno[1] = $retornoManual[1];
				return $retorno;
			}
		}
		else
		{
			//Mant�m a imagem antiga
			$retornoManual[1] = $post['manual'];
		}

		$sql = "
			UPDATE	
				".$this->entidade."
			SET
				titulo			= '". $post['titulo'] ."',
				especial		= '". $post['especial'] ."',
				destaque		= '". $post['destaque'] ."',
				descricao		= '".utf8_decode($post['descricao'])."',
				manual			= '".$retornoManual[1]."',
				caminhoImagem	= '".$retornoClassImagem[1]."'
			WHERE
				id 				= '".$post['id']."'
		".$query;
		$result = mysql_query($sql);
		if (!($result))
		{
			$retorno[0] = "1";
			$retorno[1] = "Erro ao executar a query. Classe = ".$this->entidade." - Metodo = Alterar";
			return $retorno;
		}

		//Exclui o v�nculo com a categoria
		$retornoExclusao = $this->Exclui_categoria_produto($post['id']);
		if( $retornoExclusao[0] )
		{
			$retorno[0] = "1";
			$retorno[1] = "N�o foi poss�vel excluir o v�nculo com a categoria!";
			return $retorno;
		}

		//Inclui novamente o v�nculo com a categoria
		$idProduto = $post['id'];
		if($idProduto > 0)
		{
			for($i=0; $i < count($post['idCategoria']); $i++)
			{
				$parametroCategoria['idProduto'] = $idProduto;
				$parametroCategoria['idCategoria'] = $post['idCategoria'][$i];

				$retorno = $this->Grava_Produto_Categoria($parametroCategoria);
				if( $retorno[0] )
				{
					$retorno[0] = "1";
					$retorno[1] = "N�o foi poss�vel vincular o produto � categoria!";
					return $retorno;
				}
			}
		}
		
		$retorno[0] = 0;
		$retorno[1] = "Altera��o feita com sucesso!";
		$retorno[2] = $idProduto;
		return $retorno;
	}
	
	function Pesquisar($post, $totalPorPagina, $pagina)
	{
		$query = "";

		$sqlLimit = "";
		if ($totalPorPagina) {

			$numero = $pagina-1;
			$_limit = $numero*$totalPorPagina;

			$sqlLimit = "LIMIT ".$_limit.",".$totalPorPagina."";
		}

		if($post['noticia'])
		{
			$query .= " AND noticias.pagina = '".$post['noticia']."' ";
		}

		if($post['ano'])
		{
			$query .= " AND YEAR(data) = '".$post['ano']."' ";
		}

		if($post['mes'])
		{
			if (intval($post['mes'])) {
				$query .= " AND MONTH(data) = '".$post['mes']."' ";
			}else{
				$retorno = MesInverte($post['mes']);
				$query .= " AND MONTH(data) = '".$retorno."' ";
			}			
		}

		$retorno = array();
		$sql = "SELECT 
					*
				FROM
					" . $this->entidade . " 
				WHERE
					1 = 1 ".$query."
				ORDER BY
					id DESC
			".$sqlLimit." ";

		// echo "<pre>";
		// print_r($sql);
		// die();

		$result = mysql_query($sql);
		if (!($result))
		{
			$retorno[0] = "1";
			$retorno[1] = "Erro ao executar a query. Classe = " . $this->entidade . " - Metodo = Pesquisar";
			return $retorno;
		}
		
		$i = 0;
		while( $rows = mysql_fetch_array($result) )
		{
			$dados[$i] 					= $rows;
			$dados[$i]['titulo'] 		= utf8_encode($rows['titulo']);
			$dados[$i]['texto'] 		= utf8_encode($rows['texto']);
			$dados[$i]['data'] 			= date("d/m/Y", strtotime($rows['data']));
			$dados[$i]['Mes']			= Mes(explode("-", ($rows['data'])));
			$dados[$i]['dataMes'] 		= explode("-", ($rows['data']));
			$i++;
		}
		
		$retorno[0] = 0;
		$retorno[1] = $dados;
		return $retorno;
	}

	function PesquisarMenuLateral($post)
	{

		$query = "";

		$retorno = array();

		$sql = "SELECT 
					data
				FROM
					" . $this->entidade . " 
				WHERE
					1 = 1 ".$query."
				GROUP BY
					MONTH(data), YEAR(data)
				ORDER BY
					YEAR(data) DESC, MONTH(data) DESC
			";

		$result = mysql_query($sql);
		if (!($result))
		{
			$retorno[0] = "1";
			$retorno[1] = "Erro ao executar a query. Classe = " . $this->entidade . " - Metodo = Pesquisar";
			return $retorno;
		}
		
		$i = 0;
		while( $rows = mysql_fetch_array($result) )
		{
			$dados[$i]['data'] 			= date("d/m/Y", strtotime($rows['data']));
			$dados[$i]['Mes']			= Mes(explode("-", ($rows['data'])));
			$dados[$i]['dataMes'] 		= explode("-", ($rows['data']));
			$i++;
		}
		
		$retorno[0] = 0;
		$retorno[1] = $dados;
		return $retorno;
	}

	// function PesquisarMenuLateral($post)
	// {

	// 	$query = "";

	// 	$retorno = array();

	// 	$sql = "SELECT 
	// 				data
	// 			FROM
	// 				" . $this->entidade . " 
	// 			WHERE
	// 				1 = 1 ".$query."
	// 			GROUP BY
	// 				MONTH(data)
	// 			ORDER BY
	// 				id DESC
	// 		";

	// 	$result = mysql_query($sql);
	// 	if (!($result))
	// 	{
	// 		$retorno[0] = "1";
	// 		$retorno[1] = "Erro ao executar a query. Classe = " . $this->entidade . " - Metodo = Pesquisar";
	// 		return $retorno;
	// 	}
		
	// 	$i = 0;
	// 	while( $rows = mysql_fetch_array($result) )
	// 	{
	// 		$dados[$i]['data'] 			= date("d/m/Y", strtotime($rows['data']));
	// 		$dados[$i]['Mes']			= Mes(explode("-", ($rows['data'])));
	// 		$dados[$i]['dataMes'] 		= explode("-", ($rows['data']));
	// 		$i++;
	// 	}
		
	// 	$retorno[0] = 0;
	// 	$retorno[1] = $dados;
	// 	return $retorno;
	// }



	function Pesquisar_Produto_Categoria($post)
	{
		$query = "";

		if($post['idProduto'])
		{
			$query .= " AND PC.idProduto = '".$post['idProduto']."' ";
		}
		
		$retorno = array();
	
		$sql = "SELECT
					*
				FROM  
					produtocategoria PC
				INNER JOIN categoria C
					ON PC.idCategoria = C.id
				WHERE
					1 = 1 ".$query."
				ORDER BY
					C.id DESC
		";
		$result = mysql_query($sql);
		if (!($result))
		{
			$retorno[0] = "1";
			$retorno[1] = "Erro ao executar a query. Classe = " . $this->entidade . " - Metodo = Pesquisar";
			return $retorno;
		}
		
		$i = 0;
		while( $rows = mysql_fetch_array($result) )
		{
			$dados[$i] 					= $rows;
			$dados[$i]['titulo'] 		= utf8_encode($rows['titulo']);

			$i++;
		}
		
		$retorno[0] = 0;
		$retorno[1] = $dados;
		return $retorno;
	}

	function Exclui_categoria_produto($id)
	{
		$retorno = array();
		
		$sql = "
			DELETE FROM	
				produtocategoria
			WHERE
				idProduto = '".$id."'
		".$query;
		$result = mysql_query($sql);
		if (!($result))
		{
			$retorno[0] = "1";
			$retorno[1] = "Erro ao executar a query. Classe = ".$this->entidade." - Metodo = Exclui_categoria_produto";
			return $retorno;
		}
		
		$retorno[0] = 0;
		$retorno[1] = "Exclus�o feita com sucesso!";
		return $retorno;
	}
	
	function Exclui($id)
	{
		$retorno = array();

		// Localiza imagem para exclus�o do registro
		$parametro['id'] = $id;
		$dados = $this->Pesquisar($parametro, null, null);
		if( $dados[0] )
		{
			$retorno[0] = "1";
			$retorno[1] = "N�o foi poss�vel localizar a imagem para excluir!";
			return $retorno;
		}
		$imgExclusao = $dados[1][0]["caminhoImagem"];

		// Localiza imagem para exclus�o do registro
		$parametro2['id'] = $id;
		$dados2 = $this->Pesquisar($parametro2, null, null);
		if( $dados2[0] )
		{
			$retorno[0] = "1";
			$retorno[1] = "N�o foi poss�vel localizar a imagem para excluir 2!";
			return $retorno;
		}
		$imgExclusao2 = $dados[1][0]["manual"];
		
		$sql = "
			DELETE FROM	
				".$this->entidade." 
			WHERE
				id = '".$id."'
		".$query;
		$result = mysql_query($sql);
		if (!($result))
		{
			$retorno[0] = "1";
			$retorno[1] = "Erro ao executar a query. Classe = ".$this->entidade." - Metodo = Exclui";
			return $retorno;
		}

		//Exclui imagem da pasta
		if($imgExclusao != "")
		{ 
			if(!unlink( $this->pathProjeto.$imgExclusao ))
			{
				$retorno[0] = "1";
				$retorno[1] = "N�o foi poss�el excluir a imagem do registro!";
				return $retorno;
			}
		}

		//Exclui imagem da pasta
		if($imgExclusao2 != "")
		{ 
			if(!unlink( $this->pathProjeto.$imgExclusao2 ))
			{
				$retorno[0] = "1";
				$retorno[1] = "N�o foi poss�el excluir manual!";
				return $retorno;
			}
		}

		//Exclui o v�nculo com o modelo
		$retornoExclusao = $this->Exclui_categoria_produto($id);
		if( $retornoExclusao[0] )
		{
			$retorno[0] = "1";
			$retorno[1] = "N�o foi poss�vel excluir o v�nculo com a categoria!";
			return $retorno;
		}

		
		$retorno[0] = 0;
		$retorno[1] = "Exclus�o feita com sucesso!";
		return $retorno;
	}

}

