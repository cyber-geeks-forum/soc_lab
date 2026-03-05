<?php

switch ($_SESSION["role_name"]) {

    case "super_admin":
        header("Location: ../dashboards/super_admin/dashboard.php");
        break;

    case "department_admin":
        header("Location: ../dashboards/department_admin/dashboard.php");
        break;

    case "member":
        header("Location: ../dashboards/member/dashboard.php");
        break;

    default:
        session_destroy();
        header("Location: ../public/login.php");
      
        exit;
}