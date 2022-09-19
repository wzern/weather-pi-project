<?php

$passwordIn = $_GET['password'];
echo password_hash($passwordIn, PASSWORD_DEFAULT);