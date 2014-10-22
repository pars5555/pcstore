<div style="left: 10px;right:10px; bottom:40px;top:40px;position: absolute;font-size: 18px">
    <div style="position:absolute; left:50%;top:50%;width:500px;height:200px; margin-top:-100px;margin-left:-250px">
        <form id="checkout_check_password_form">
            <p>
                <label class="grid_15" style="float: left">{$ns.langManager->getPhraseSpan(73)}</label>
                <span class="grid_30" style="float: left">{$ns.user->getEmail()}</span>
            </p>
            <p> </p>
            <p>
                <label for="checkout_password_check_pass" class="grid_15" style="float: left;"> {$ns.langManager->getPhraseSpan(4)}: </label>
                <input id='checkout_password_check_pass' name="pass" type="password" autocomplete="off" class="grid_10" style="float: left"/>
            </p>
        </form>
    </div>
</div>