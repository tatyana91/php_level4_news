<?php
$title = Core::clearStr($_POST['title']);
$category = Core::clearInt($_POST['category']);
$description = Core::clearStr($_POST['description']);
$text = Core::clearStr($_POST['text']);
$source = Core::clearStr($_POST['source']);

if (!$title || !$category || !$description || !$source|| !$text){
    $errorMsg = "Заполните все поля формы!";
}
else {
    $result = $news->saveNews($title, $category, $description, $text, $source);
	if (!$result) {
		$errorMsg = 'Произошла ошибка при добавлении новости';
	}
	else {
		header('Location: news.php');
		exit();
	}
}
