<?php
session_start();

$helper = $fb->getRedirectLoginHelper();
$loginUrl = $helper->getLoginUrl('https://lachris.tk/callback');
echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';