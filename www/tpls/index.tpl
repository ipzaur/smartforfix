<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{if:(title:)}{title:} - {:fi}SmartForFix. Ремонт Smart'ов своими руками.</title>
    <meta charset="utf-8">
    <meta content="IE=9" http-equiv="X-UA-Compatible">
    <meta content="Персональное хранилище ссылок" name="description">
    <meta content="Smart, Смарт, ForTwo, ForFour, Roadster, ремонт, Smartcar, smartfortwo, smartforfour, smartroadster" name="keywords">
    <link type="text/css" rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:100normal,100italic,300normal,300italic,400normal,400italic,500normal,500italic,700normal,700italic,900normal,900italic&amp;subset=all">
    <link href="{siteurl:}css/s4fx.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript">var isMobile = {isMobile:};</script>
</head>
<body>
    <header class="head">
        <div class="content">
            <a class="head_logo" href="{siteurl:}"><img src="{siteurl:}/pics/svg/logo.svg"></a>
            {if:(user:)}{+auth/profile:}{else:}{+auth/login:}{:fi}
            <!--input class="head_search" type="text" value="" name="search" placeholder="Поиск"-->
        </div>
    </header>

    {+menu:}

    {if:(profile:)}
        {+profile:}
    {else:}
        {if:(article_edit)}
            {+article/edit:}
        {else:}
            {if:(article_list)}{+article/list:}
            {else:}
                {if:(article)}{+article/show:}
                {:fi}
            {:fi}
            <footer class="foot">
                <div class="content">
                    <p class="foot_copy">SmartForFix, 2014</p>
                    <!-- p class="foot_links"><a href="">О проекте</a></p -->
                </div>
            </footer>
        {:fi}
    {:fi}

    <script type="text/javascript" src="{siteurl:}js/s4fx.js" defer="defer"></script>
</body>
