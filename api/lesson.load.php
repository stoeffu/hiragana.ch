<?php
require_once('config.php');

$courseId = htmlspecialchars($_POST['course_id']);
$lessonId = htmlspecialchars($_POST['lesson_id']);

$url = PAGE_DIR."lessons/$courseId-$lessonId.html";
$file = file_get_contents($url);
echo $file;
