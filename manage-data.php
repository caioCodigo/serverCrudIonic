<?php
 // Define database connection parameters
 $hn      = 'localhost';
 $un      = 'root';
 $pwd     = '';
 $db      = 'ionic_crud';
 $cs      = 'utf8';


 // Set up the PDO parameters
 $dsn 	= "mysql:host=" . $hn . ";port=3306;dbname=" . $db . ";charset=" . $cs;
 $opt 	= array(
                      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                      PDO::ATTR_EMULATE_PREPARES   => false,
                     );
 // Create a PDO instance (connect to the database)
 $pdo 	= new PDO($dsn, $un, $pwd, $opt);


 // Retrieve the posted data
 $json    =  file_get_contents('php://input');
 $obj     =  json_decode($json);
 $key     =  strip_tags($obj->key);

 
 // Determine which mode is being requested


  function formataImg($objeto){
     $imagem = $objeto;
    list($type, $imagem) = explode(';', $imagem);
    list(,$extension) = explode('/',$type);
    list(,$imagem)      = explode(',', $imagem);
    $fileName =  "uploads/".uniqid().'.'.$extension;
    $imageData = base64_decode($imagem);
    file_put_contents($fileName, $imageData);

    $link = str_replace("/manage-data.php","","$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]/");
    $actual_link = "http://$link"."$fileName";

    return $actual_link;
 }
 switch($key)
 {

    // Add a new record to the technologies table
    case "create":
  

       // Sanitise URL supplied values

       $id        = filter_var($obj->id, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
       $login 	  = filter_var($obj->login, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
       $senha	  = filter_var($obj->senha, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
       $nome	  = filter_var($obj->nome, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
       $endereco  = filter_var($obj->endereco, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
       $imagem    = filter_var($obj->imagem, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
       $tipoUsuario = filter_var($obj->tipo, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
       $urlImagem = formataImg($imagem);

    


      
       // Attempt to run PDO prepared statement
       try {
        
        
          $sql 	= "INSERT INTO usuarios(id_usu,login_usu, senha_usu, nome_usu, endereco_usu,imagem_usu,tipo_usu) VALUES(:id,:login, :senha, :nome, :endereco, :data, :tipo)";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':id', $id, PDO::PARAM_STR);
          $stmt->bindParam(':login', $login, PDO::PARAM_STR);
          $stmt->bindParam(':senha', $senha, PDO::PARAM_STR);
          $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
          $stmt->bindParam(':endereco', $endereco, PDO::PARAM_STR);
          $stmt->bindParam(':data', $urlImagem, PDO::PARAM_STR);
          $stmt->bindParam(':tipo', $tipoUsuario, PDO::PARAM_STR);
          $stmt->execute();

          echo json_encode(array('message' => 'Congratulations the record','tste' => $urlImagem));

       
       }
       // Catch any errors in running the prepared statement
       catch(PDOException $e)
       {
          echo $e->getMessage();
       }

    break;



    // Update an existing record in the technologies table
    case "update":

       // Sanitise URL supplied values
       $id 	      = filter_var($obj->id, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
       $login 	  = filter_var($obj->login, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
       $senha	  = filter_var($obj->senha, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
       $nome	  = filter_var($obj->nome, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
       $endereco  = filter_var($obj->endereco, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
       $imagem	  = filter_var($obj->imagem, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
       $tipoUsuario = filter_var($obj->tipo, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
       $urlImagem = formataImg($imagem);


       // Attempt to run PDO prepared statement
       try {
          $sql 	= "UPDATE usuarios SET login_usu = :login, senha_usu = :senha, nome_usu = :nome, endereco_usu = :endereco, imagem_usu = :imagem, tipo_usu = :tipo WHERE id_usu = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':id', $id, PDO::PARAM_STR);
          $stmt->bindParam(':login', $login, PDO::PARAM_STR);
          $stmt->bindParam(':senha', $senha, PDO::PARAM_INT);
          $stmt->bindParam(':nome', $nome, PDO::PARAM_INT);
          $stmt->bindParam(':endereco', $endereco, PDO::PARAM_INT);
          $stmt->bindParam(':imagem', $urlImagem, PDO::PARAM_INT);
          $stmt->bindParam(':tipo', $tipoUsuario, PDO::PARAM_STR);

          $stmt->execute();

          echo json_encode('Congratulations the record ' . $login . ' was updated');
       }
       // Catch any errors in running the prepared statement
       catch(PDOException $e)
       {
          echo $e->getMessage();
       }

    break;



    // Remove an existing record in the technologies table
    case "delete":

       // Sanitise supplied record ID for matching to table record
       $recordID	= filter_var($obj->recordID, FILTER_SANITIZE_NUMBER_INT);

       // Attempt to run PDO prepared statement
       try {
          $pdo 	= new PDO($dsn, $un, $pwd);
          $sql 	= "DELETE FROM usuarios WHERE id_usu = :recordID";
          $stmt 	= $pdo->prepare($sql);
          $stmt->bindParam(':recordID', $recordID, PDO::PARAM_INT);
          $stmt->execute();

          echo json_encode('Congratulations the record  was removed');
       }
       // Catch any errors in running the prepared statement
       catch(PDOException $e)
       {
          echo $e->getMessage();
       }

    break;
 }
?>