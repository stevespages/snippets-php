<?php

// Note that this function implements a form of whitelisting of user entered $_POST data. Obviously...
// ... the user entered values can not be anticipated but the names of the keys of $_POST elements ...
// ... are only accessed if they are in the $form_array. There is no mechanism to change the values of ...
// ... name elements in the $form_array except by hardcoding them. 
function assignPostToFormArray($form_array)
{
	foreach($form_array as $key => $array)
	{
		$form_array[$key]['value'] = htmlentities($_POST[$form_array[$key]['name']]);
	}
	return $form_array;
}

// $pdo and $table_name arguments are only required if using a validation test which requires access to the database.
// A test needs to recognise 
function validateFormArray($form_array, $pdo=null, $table_name=null)
{
	
	foreach($form_array as $key => $array)
	{
		// from Symfony documentation web site
		if($form_array[$key]['validate']=='not_blank')
		{
			if(false === $form_array[$key]['value'] || (empty($form_array[$key]['value']) && '0' != $form_array[$key]['value']))
			{
				$form_array[$key]['error_mssg'] .= 'This field can not be empty';
			}
		}
		if($form_array[$key]['validate']=='FILTER_VALIDATE_EMAIL')
		{
			if (!filter_var($form_array[$key]['value'], FILTER_VALIDATE_EMAIL))
			{
    			$form_array[$key]['error_mssg'] .= "Please enter a valid email address";
			}	
		}
		if($form_array[$key]['validate']=='unique')
		{
			$column  = $form_array[$key]['name'];
			$sql = ("SELECT * FROM $table_name WHERE $column = ?");
			$stmt = $pdo->prepare($sql);
			$stmt->execute([$form_array[$key]['value']]);
			$row = $stmt->fetchObject();
			if($row !=  null)
			{
				$form_array[$key]['error_mssg'] .= 'That is already in the database. Please use another value';
			}
		}
	}
	
	return $form_array;
}


function isFormValid($form_array)
{
	$is_form_valid = true;
	foreach($form_array as $key => $array)
	{
		if($form_array[$key]['error_mssg'] != "")
		{
			$is_form_valid = false;
		}
	}
	return $is_form_valid;
}


function showForm($action, $form_array)	{
	$form = "<form method='post' action=".$action.">";
	foreach($form_array as $key => $value) {
		$form .= "<p><label>".$form_array[$key]['form_label'];
		if($form_array[$key]['type']=='select') {
			$form .= " <select name='".$form_array[$key]['name']."'>";
			foreach($form_array[$key]['options'] as $key_2 => $value_2) {
				$form .= "<option value=".$key_2.">".$value_2."</option>";
			}
			$form .= "</select";
		} else {
			
			/*
			$form .= " <input name='".$form_array[$key]['name']
				."' type='".$form_array[$key]['type']
				."' value='".$form_array[$key]['value']
				."'";
			*/
			
			$form .= ' <input name="'.$form_array[$key]["name"]
				.'" type="'.$form_array[$key]["type"]
				.'" value="'.$form_array[$key]["value"]
				.'"';
				
			if($form_array[$key]['required']=='required') {
				$form .=" required";
			}				
		}
	$form .=	"></label> ".$form_array[$key]['error_mssg']."</p>";
	}
	$form .= "<input type='submit'> <a href='index.php'> Cancel </a></form></br>";
	return $form;
}

// Creates an HTML table from an SQL SELECT statement.
function createTable($statement, $editable=null)
{
	$table = "<div><table border=1>";
	while($row = $statement->fetchObject())
	{
		$table .= "<tr>";
		foreach($row as $key => $value)
		{
			$table .= "<td>".$value."</td>";
		}
		if($editable) {
			$table .= "<td><a href='index.php?pg=edit&id=".$row->id."'>Edit?</a></td>";
			$table .= "<td><a href='index.php?pg=delete&id=".$row->id."'>Delete?</a></td>";
		}
		$table .= "</tr>";
	}
	$table .= "</table></div>";
	return $table;
}

/*
// Creates SQL query to make a table from a form_array.
// Needs work!!
function creatSQLTable($form_array)
{
    $table_name = str_replace('_form_array', '', $form_array);
    $sql = "CREATE TABLE $table_name (";
    foreach($form_array as $key => $value) {
    	$sql .= $key. " " $form_array[$key]['column_type'].", ";
    	
}
*/

