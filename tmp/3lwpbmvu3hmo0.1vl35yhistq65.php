<style>
    .upper-panel {
        width: 100vw;
        background: rgba(95, 104, 153, .9);
    }
    .upper-panel ul {
        padding-left: 0;
        list-style: none;

        display: flex;
        font-size: 1.1em;
        padding: 8px;
    }
    .upper-panel ul li {
        margin: 0 1em;
    }
    .upper-panel .currency {
        margin-left: auto;
        margin-right: 0;
    }
    .currency {
        margin-right: 0;
        margin-left: auto;
        text-align: right;
    }
</style>
<div class="upper-panel">
    <ul >
        <li>
            Server : <?= ($SESSION['server'])."
" ?>
        </li>
        <li>
            Login : <?= ($SESSION['login'])."
" ?>
        </li>
        <li class="currency">
            Gold : <?= ($SESSION['currency'])."
" ?>
        </li>
    </ul>
</div>