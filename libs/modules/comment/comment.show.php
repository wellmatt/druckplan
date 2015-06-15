<?php
require_once 'libs/modules/comment/comment.class.php';

$url = Comment::showComment($_REQUEST["id"]);

echo '<script language="JavaScript">window.location.href = "index.php?page='.$url.'"</script>';