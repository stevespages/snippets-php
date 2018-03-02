<?php

include_once '../functions/functions.php';

// CONFIG
// Dev
$host = '127.0.0.1';
$db   = 'snippets';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$user = 'root';
$pass = '';
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

/*
// Prod
$host = 'db719301055.db.1and1.com';
$db   = 'db719301055';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$user = 'root';
$pass = '12345678';
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
*/

$pdo = new PDO($dsn, $user, $pass, $opt);

// MODEL
// $form_arrays:...
// ...primary keys are the values of the name attributes of the form input elements...
// ... and the names of the corresponding columns in a MySQL (or other) table.
// Most but not necessarily all the secondary keys are required to avoid an 'undefined index' error.

// VIEW

// CONTROLLER

function home($pdo)
{
    $snippet_form_array = array ( 
			"snippet" => array (
						"name" => "snippet",
						"required" => "required",
						"value" => "",
						"error_mssg" => "",
						"form_label" => "Snippet",
						"type" => "text",
						"validate" => "unique"
						),
			"description" => array (
						"name" => "description",
						"required" => "",
						"value" => "",
						"error_mssg" => "",
						"form_label" => "Describe",
						"type" => "text",
						"validate" => "string"
						),
			"importance" => array (
						"name" => "importance",
						"required" => "",
						"value" => "",
						"error_mssg" => "",
						"form_label" => "Importance",
						"type" => "text",
						"validate" => "string"
						),
			"language" => array 	(
						"name" => "language",
						"required" => "",
						"value" => "",
						"error_mssg" => "",
						"form_label" => "Language",
						"type" => "select",
						"options" => array (
										"" => "",
										"php" => "PHP",
										"sql" => "SQL",
										"bash" => "Bash"
										),
						"validate" => ""
						)
    );
    $action = "index.php";
    If (isset($_GET['id'])) {
        $id = htmlentities($_GET['id']); // this is necessary for editing and deleting a particular row
    }

    If ($_SERVER['REQUEST_METHOD'] == 'POST')	{
        $snippet_form_array = assignPostToFormArray($snippet_form_array);
        $snippet_form_array = validateFormArray($snippet_form_array, $pdo, 'snip');
        $is_form_valid = isFormValid($snippet_form_array);

	
        if($is_form_valid === true AND !isset($_GET['id'])) {
            save('snip', $snippet_form_array, $pdo); // saves post as a new row
        }
	
	     // $_GET['id'] is set because the value of the form's action attribute was set...
	     // ...to ?id=.$id when the form was generated from clicking on the Edit hyperlink
	     if($is_form_valid === true AND isset($_GET['id'])) {
		      updateRow('snip', $snippet_form_array, $id, $pdo);  // updates a row from an edited post
	     }
	
	     // if $is_form_valid does not === true then $form_array needs to be passed to showForm() with...
	     // ...its values and error messages intact.
	     // if $is_form_valid === true then the job has been done and we need to refresh the page for a new start.
        if($is_form_valid === true) {
            header('Location: index.php');
        }
    }

    If ($_SERVER['REQUEST_METHOD'] != 'POST' AND isset($_GET['id']))	{
        if($_GET['pg']=='edit') {
            $action .= "?id=".$id;
				$statement = getRowToEdit('snip', $id, $pdo); // gets a row to be edited using id of the row
				$row = $statement->fetchObject();
				foreach($snippet_form_array as $key => $array) {
					$snippet_form_array[$key]['value'] = $row->$key;
				}			
        }
        if($_GET['pg']=='delete') {
            deleteRow('snip', $id, $pdo); // deletes a row using id of the row
            header('Location: index.php');
        }
    }
    $snippet_form = showForm($action, $snippet_form_array);
    $statement = getAll('snip', $pdo);
    $snippet_table = createTable($statement, true);
    $home = array ($snippet_form, $snippet_table);
    return $home;
}

$contrl = getControllerName();
$navigation = array("index.php");
$title = ucfirst($contrl);
$title = str_replace('-', ' ', $title);
$content = $contrl($pdo);

// TEMPLATE
$template = '
<!DOCTYPE html>
<html lang="en">
<head>
  <title>'.$title.'</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container-fluid">
<h1>Snippets</h1>
<div class="row">
  <div class="col-sm">'.$content[0].'</div>
</div>

<div class="row">
  <div class="col-sm">'.$content[1].'</div>
</div>

</div>

</body>
</html>';

echo $template;
