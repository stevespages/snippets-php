<?php

include_once '../functions/functions.php';
include_once 'config.php';

// primary keys are the values of the name attributes of the form input elements...
// ... and the names of the corresponding columns in a MySQL (or other) table.
// Most but not necessarily all the secondary keys are required to avoid an 'undefined index' error.
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

//$pdo = new PDO("mysql:host=localhost;dbname=snippets;charset=utf8mb4", "root", "");
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
$page = include_once 'template.php';
echo $page;

/*
echo '<!DOCTYPE html><html><head><meta charset="utf-8" /><title></title></head>';
//echo '<p>'.$row.'</p>';
echo '<h2>Snippets</h2>'.$snippet_form.'</br>'.$snippet_table.'</br>';
echo '<body></body></html>';
*/
