<?php
echo 'save_path=' . session_save_path() . '<br>';
echo 'writable=' . (is_writable(session_save_path() ?: sys_get_temp_dir()) ? 'YES' : 'NO') . '<br>';
session_start();
$_SESSION['n'] = ($_SESSION['n'] ?? 0) + 1;
echo 'session_id_len=' . strlen(session_id()) . '<br>';
echo 'counter=' . $_SESSION['n'] . '<br>';
