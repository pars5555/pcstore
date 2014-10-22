<div style="position: relative; width: 100%;">

    {if $ns.checkPassword == 1}
        <div style="position:absolute;top:100px;width:100%;text-align: center">
            {if isset($ns.message)}
                <div style="color:red;margin:10px;font-size: 16px">
                    {$ns.message}
                </div>
            {/if} 
            <form id="yp_password_form">
                <input id="yp_password_check" type="password" autocomplete="off" placeholder="{$ns.langManager->getPhrase(4)}"/>
                <div style="clear:both;margin-bottom: 10px"></div>
                <button class="button blue">{$ns.langManager->getPhraseSpan(582)}</button>
            </form>
        </div>
    {else}

        <div style="text-align: center;margin:20px">
            <h1>{$ns.langManager->getPhraseSpan(79)}</h1>
        </div >

        <form id='yp_form' name='yp_form'  style="padding: 10px;margin: 10px;">
            <p id = "yp_error_p"  style="display: none">
                <span class="error"> </span>
                <span id="yp_error_message" style="color:#FF4422;" ></span>
            </p>

            <p>
            <div style="line-height:20px;float: left;width:110px;text-align: left;">
                <label for="email">{$ns.langManager->getPhraseSpan(73)}: </label>
            </div>
            <input name='email' type="text" readonly="readonly" 
                   value="{if $ns.user->getLoginType()!='pcstore'}{$ns.user->getLoginType()} {/if}{$ns.userManager->getRealEmailAddressByUserDto($ns.user)}"/>
            </p>

            <div style="width: 50%;float: left;">
                <p>
                <div style="line-height:20px;float: left;width:110px;text-align: left;">
                    <label for="name">{$ns.langManager->getPhraseSpan(61)}:</label>
                </div>

                <input name='name' type="text" value="{$ns.user->getName()}"/>
                </p>
                <p>
                <div style="line-height:20px;float: left;width:110px;text-align: left;">
                    <label for="lname">{$ns.langManager->getPhraseSpan(81)}:</label>
                </div>

                <input  name='lname' type="text"  value="{$ns.user->getLastName()}"/>
                </p>
                {if $ns.userLoginType ==='pcstore'}
                    <p>
                    <div style="line-height:20px;float: left;width:110px;text-align: left;">
                        <label for="change_pass">{$ns.langManager->getPhraseSpan(27)}:</label>
                    </div>

                    <input  name='change_pass' type="checkbox" id="password_change_checkbox"/>
                    </p>

                    <div id="change_password_div" style="padding-left: 20px;">
                        <p>
                        <div style="line-height:20px;float: left;width:130px;text-align: left;">
                            <label for="new_pass">{$ns.langManager->getPhraseSpan(28)}:</label>
                        </div>

                        <input  name='new_pass' type="password"/>
                        </p>

                        <p>
                        <div style="line-height:20px;float: left;width:130px;text-align: left;">
                            <label for="repeat_new_pass">{$ns.langManager->getPhraseSpan(29)}:</label>
                        </div>

                        <input  name='repeat_new_pass' type="password"/>
                        </p>
                    </div>
                {/if}
                <p>
                <div style="line-height:20px;float: left;width:110px;text-align: left;">
                    <label for="phone1">{$ns.langManager->getPhraseSpan(75)} 1:</label>
                </div>

                <input  name='phone1' type="text" value="{$ns.phones[0]}" />
                </p>
                <p>
                <div style="line-height:20px;float: left;width:110px;text-align: left;">
                    <label for="phone2">{$ns.langManager->getPhraseSpan(75)} 2:</label>
                </div>

                <input name='phone2' type="text" value="{$ns.phones[1]}"/>
                </p>
                <p>
                <div style="line-height:20px;float: left;width:110px;text-align: left;">
                    <label for="phone3">{$ns.langManager->getPhraseSpan(75)} 3:</label>
                </div>

                <input name='phone3' type="text" value="{$ns.phones[2]}"/>
                </p>
                <p>
                <div style="line-height:20px;float: left;width:110px;text-align: left;">
                    <label for="address">{$ns.langManager->getPhraseSpan(13)}:</label>
                </div>

                <input name='address' type="text" value="{$ns.user->getAddress()}"/>
                </p>
                <p>
                <div style="line-height:20px;float: left;width:110px;text-align: left;">
                    <label for="region">{$ns.langManager->getPhraseSpan(45)}:</label>
                </div>

                <select onkeyup="this.blur();
                        this.focus();" name='region' class="cmf-skinned-select cmf-skinned-text" style="width:110px" >
                    {foreach from=$ns.regions_phrase_ids_array item=value key=key}
                        <option value="{$ns.langManager->getPhrase($value, 'en')}" {if $ns.region_selected == $ns.langManager->getPhrase($value, 'en')}selected="selected"{/if} class="translatable_element" phrase_id="{$value}">{$ns.langManager->getPhrase($value)}</option>
                    {/foreach}		

                </select>

                </p>

            </div>

            <div style="width: 50%;float: left;">
                <div style="margin-bottom: 30px">
                    <div style="line-height:30px;float: left;width:120px;text-align: left;font-size: 12px">
                        <span >{$ns.langManager->getPhraseSpan(441)}:</span>
                    </div>
                    <input type="text" style="width:280px;font-size: 12px;color:#006600; height: 30px;float:left" readonly="readonly" value="{$SITE_PATH}?invc={$ns.user->getSubUsersRegistrationCode()}"/>

                    <div style="text-align: center">
                        <a href="#" 
                           onclick="
                                   var sharer = '//www.facebook.com/sharer/sharer.php?s=100&p[url]=' +
                                           encodeURIComponent('{$SITE_PATH}?invc={$ns.user->getSubUsersRegistrationCode()}') + '&p[images][0]=' + '{$SITE_PATH}/img/logo_150x91.png' +
                                                           '&p[title]=PcStore.am All importer companies items in one place' + '&p[summary]=';
                                                   window.open(sharer,
                                                           'fb-share-dialog',
                                                           'width=626,height=436');
                                                   return false;">	
                            <img src="{$SITE_PATH}/img/social/fb-share.png"/>
                        </a>

                    </div>
                    <span style="color:#A00">{$ns.langManager->getPhraseSpan(442)}</span>
                </div>

                {if $ns.user->getVip()==1}
                    <div>					
                        <div style="line-height:20px;float: left;width:120px;text-align: left;font-size: 12px">
                            <span>{$ns.langManager->getPhraseSpan(448)}:</span>
                        </div>						
                        <input id="up_enable_vip" type="checkbox" name="enable_vip" style="float:left" value="1" {if $ns.user->getEnableVip()==1} checked="checked" {/if}/>							
                    </div>
                {/if}
            </div>
            <div style="clear:both"></div>
        </form>
        <div style="clear:both"></div>
        <div id = "yp_bottom_div" style=" width:100%;margin-top:50px; text-align: center;">

            <button id="yp_save_button" class="button blue">
                {$ns.langManager->getPhraseSpan(43)}

            </button>
            <button id="yp_reset_button" class="button glyph">
                {$ns.langManager->getPhraseSpan(44)}
            </button>
        </div>
        <div style="clear:both"></div>
    {/if}
</div>
