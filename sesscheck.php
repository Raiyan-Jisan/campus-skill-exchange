<?php
session_start();
$_SESSION['n'] = ($_SESSION['n'] ?? 0) + 1;
echo 'session_id_len=' . strlen(session_id()) . '<br>';
echo 'counter=' . $_SESSION['n'] . '<br>';
echo 'host=' . gethostname() . '<br>';
