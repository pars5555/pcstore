<div id= "user_registration_div">
    <div id="fl_forgot_error_msg" style="color:red;margin: 5px"></div>
    <form id='ur_form' name='ur_form'  style="padding: 10px;margin: 10px;" autocomplete="off">
        <div id = "error_p"  style="display: none;">
            <span class="error"> </span>
            <span id="error_message" style="color:#FF4422;" > </span>
        </div>

        <div style="line-height:20px;float: left;width:150px;text-align: left;">
            <label for="email" >{$ns.langManager->getPhraseSpan(73)}* :</label>
        </div>
        <input id ='email'  name='email' type="email"/>
        <div style="clear: both;margin-top: 20px"></div>

        <div style="line-height:20px; float: left;width:150px;text-align: left;">
            <label for="name" >{$ns.langManager->getPhraseSpan(74)}* :</label>
        </div>
        <input id ='name' name='name' type="text"/>
        <div style="clear: both;margin-top: 20px"></div>


        <div style="line-height:20px;float: left;width:150px;text-align: left;">
            <label for="phone" >{$ns.langManager->getPhraseSpan(75)} :</label>
        </div>
        <input  name='phone' type="text"/>			
        <div style="clear: both;margin-top: 20px"></div>

        <div style="line-height:20px;float: left;width:150px;text-align: left;">
            <label for="pass">{$ns.langManager->getPhraseSpan(4)}* :</label>
        </div>
        <input  id ='pass' name='pass' type="password"/>
        <div style="clear: both;margin-top: 20px"></div>

        <div style="line-height:20px;float: left;width:150px;text-align: left;">
            <label for="repeat_pass">{$ns.langManager->getPhraseSpan(77)}* :</label>
        </div>
        <input  id ='repeat_pass' name='repeat_pass' type="password"/>
        <div style="clear: both;margin-top: 40px"></div>
        <div style="color: #800">
            * {$ns.langManager->getPhraseSpan(585)}
        </div>
    </form>

    {* for social signup*}
    <div style="float: right;">
        <img id="r_facebookLoginBtn" style="width: 35px;cursor: pointer" src="{$SITE_PATH}/img/social/fb.png"/>
        <img id="r_linkedinLoginBtn" style="width: 35px;cursor: pointer" src="{$SITE_PATH}/img/social/in.png"/>
        <span id="r_googleLoginBtn">    
            <img style="width: 35px;cursor: pointer"  src="{$SITE_PATH}/img/social/gp.png"/>
        </span>
    </div>
</div>