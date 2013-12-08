<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>SmartForFix. Ремонт Smart'ов своими руками.</title>
    <meta charset="utf-8">
    <meta content="IE=9" http-equiv="X-UA-Compatible">
    <meta content="Персональное хранилище ссылок" name="description">
    <meta content="Smart, Смарт, ForTwo, ForFour, Roadster, ремонт" name="keywords">
    <link type="text/css" rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:100normal,100italic,300normal,300italic,400normal,400italic,500normal,500italic,700normal,700italic,900normal,900italic&amp;subset=all">
    <link href="{siteurl:}css/s4fx.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript">var isMobile = {isMobile:};</script>
</head>
<body>
    <header class="head">
        <div class="content">
            {+auth:}
            <input class="head_search" type="text" value="" name="search" placeholder="Поиск">
        </div>
    </header>

    {+menu:}

    <article class="content">
        {+articleList:}
        {+paginator:}
    </article>
<!-- 
    <div class="mainblock">
        <header>
            {if:(main_page:=profile)}
                <form class="auth_form-out" method="post" action="{siteurl:}auth/">
                    <input type="hidden" name="auth_type" value="out" />
                    <button title="Выход" type="submit">Выход</button>
                </form>
            {:fi}
            <div class="h_logo"></div>
            <h1 class="h_h1">
                <span class="h_title">оставьте ссылку</span>
                <a class="h_link" href="{siteurl:}" ref="nofollow">НАПОТОМ</a>
            </h1>
        </header>
        {if:(main_page:=intro)}{+intro:}{:fi}
        {if:(main_page:=profile)}{+profile:}{:fi}
        <footer>
        </footer>
    </div> -->
    <script type="text/javascript" src="{siteurl:}js/s4fx.js" defer="defer"></script>
</body>
