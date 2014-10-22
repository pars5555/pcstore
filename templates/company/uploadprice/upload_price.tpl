<div id='upload_price_overlay_div'  class="overlay" style="display: none"></div>
<div id="upload_price_container" class="avo_style_upload_price_container">
    <div class="avo_style_uploadTopBox">
        <form id="up_price_upload_form" target="upload_target" enctype="multipart/form-data" method="post" action="{$SITE_PATH}/dyn/main/do_upload_company_price" style="float:left">
            <div style="padding: 10px;">
                {if ($ns.userLevel === $ns.userGroupsAdmin)}
                    <div class="avo_style_titleBox">
                        <label for="up_selected_company">{$ns.langManager->getPhraseSpan(66)}:</label>
                    </div>
                    <select onkeyup="this.blur();
                            this.focus();" class="cmf-skinned-select cmf-skinned-text avo_style_up_selected_company" name='up_selected_company' id='up_selected_company' autocomplete="off">
                        {html_options values=$ns.companiesIds  selected=$ns.selectedCompanyId  output=$ns.companiesNames}
                    </select>
                {else}
                    <input  id="up_selected_company" name="up_selected_company" type="hidden" value="{$ns.userId}"/>
                {/if}
                <div class="avo_style_titleBox">
                    <label for="company_price_upload">{$ns.langManager->getPhraseSpan(67)}:</label>
                    <input id="up_selected_file_name" type="text" readonly="readonly" value="{$ns.langManager->getPhrase(517)}"					   
                           class="translatable_attribute_element" attribute_phrase_id="517" attribute_name_to_translate="value"/>
                    <input id="company_price_file_input" name="company_price"  type="file" style="display:none" />
                    <input type="button" id ="select_price_file_button" class="button glyph" value="..."/>
                </div>
                <div style="float: left">
                    <label for="merge_uploaded_price_into_last_price">{$ns.langManager->getPhraseSpan(619)}: </label>
                    <input type="checkbox" name="merge_into_last_price" id = "merge_uploaded_price_into_last_price" value="1" />
                    {if ($ns.userLevel === $ns.userGroupsAdmin)}
                        </br>
                        <label for="silent_mode">{$ns.langManager->getPhraseSpan(647)}: </label>
                        <input type="checkbox" name="silent_mode" id="silent_mode" value="1" />
                    {/if}
                </div>
                <button id ="upload_company_price_button" class="button glyph">{$ns.langManager->getPhraseSpan(95)}</button>
            </div>
        </form>
        <iframe id="upload_target" name="upload_target" style="width:0;height:0;border:0px solid #fff;display: none;" ></iframe>

        {if ($ns.userLevel === $ns.userGroupsAdmin)}
            {assign var="company_id" value=$ns.selectedCompanyId}
        {else}
            {assign var="company_id" value=$ns.userId}
        {/if}
        <a  href="{$SITE_PATH}/price/last_price/{$company_id}"> 
            <div style="margin-left:30px;padding: 7px; ;float: left">

                <div style="float: left;line-height: 32px;">
                    <span style="color: green;">{$ns.langManager->getPhraseSpan(68)}:</span>
                </div>
                <div style="float: left">
                    <img src = "{$SITE_PATH}/img/document.png"  alt="zip"/> 
                </div>
            </div>
        </a>
        <a  href="javascript:void(0);" id = "revert_company_last_uploaded_price" company_id = "{$company_id}"> 
            <div style="padding: 7px; ;float: left" title="{$ns.langManager->getPhrase(492)}"
                 class="translatable_attribute_element" attribute_phrase_id="492" attribute_name_to_translate="title">
                <img src = "{$SITE_PATH}/img/revert_48x48.png"  alt="revert"/> 				
            </div>
        </a>

        {if ($ns.userLevel === $ns.userGroupsCompany)}
            <div style="clear:both"> </div> 
            <div id="f_send_price_email_container" style="padding: 20px;">


                <div id="send_email_container" style="text-align: left;"> 
                    <div style="margin-top:10px;">     
                        <div style="padding: 2px">{$ns.langManager->getPhraseSpan(614)}</div>
                        <input type="text" name='sender_email' type="text"  id="sender_email" value="{$ns.user->getEmail()}" style="width: 300px"/>
                        <input type="text" name='sender_name' type="text"  disabled id="sender_name" value="{$ns.user->getName()}" style="width: 200px"/>
                    </div> 
                    <div style="margin-top:10px;">     
                        <div style="padding: 2px">{$ns.langManager->getPhraseSpan(464)}</div>
                        <textarea name='dealer_emails' type="text"  style="width: 100%;height: 80px;resize: none;"  id="dealer_emails_textarea">{$ns.companyExProfileDto->getDealerEmails()}</textarea>                    
                        <div style="padding: 2px;text-align: right" ><span id="total_price_email_recipients_number" style="color:#F30">{$ns.total_price_email_recipients_count}</span> {$ns.langManager->getPhraseSpan(613)}</div>
                    </div> 
                    <div style="margin-top:10px">     
                        <div style="padding: 2px">{$ns.langManager->getPhraseSpan(463)}</div>
                        <input type="text" style="width:100%" id="price_email_subject" value="{$ns.companyExProfileDto->getPriceEmailSubject()}"/>
                    </div> 
                    <div style="margin-top:10px">     
                        <div style="padding: 2px">{$ns.langManager->getPhraseSpan(465)}</div>
                        <textarea name='price_email_body' type="text"  class="msgBodyTinyMCEEditor" style="width: 100%;height: 300px;resize: none;" id="price_email_body">{$ns.companyExProfileDto->getPriceEmailBody()}</textarea>
                    </div>                     
                    <div style="margin-top:5px">
                        <a  href="{$SITE_PATH}/price/last_price/{$ns.userId}"> 

                            <div style="float: left">
                                <img src = "{$SITE_PATH}/img/document.png"  alt="zip"/> 
                            </div>
                            <div style="float: left;line-height: 32px;margin-left:10px;">
                                <span style="color: green;font-size: 16px">{$ns.langManager->getPhraseSpan(466)}:</span>
                            </div>							
                        </a>

                    </div> 
                    <div style="clear:both"> </div> 
                    <div id="company_email_attachments_container" style="margin-top:5px;padding:10px">
                        <div id="attachment_element_hidden_div" style="clear:both;display: none">
                            <img style="max-width:32px;max-height:32px;vertical-align: middle"/>
                            <label></label>                            
                            <div class="up_delete_attachment f_up_delete_attachment" ></div>
                        </div>
                    </div>
                    <div style="clear:both"> </div> 
                    <div style="margin-top:5px">
                        <div style="float: left;line-height: 32px;">
                            <span style="font-size: 16px">{$ns.langManager->getPhraseSpan(615)}</span>
                        </div>							
                        <div style="float: left;margin-left:10px;">
                            <input id="company_attach_new_file_button" type="button"  class="button" value="..."/>
                        </div>
                        <form id="up_add_attachment_form" target="upload_target" enctype="multipart/form-data" method="post" action="{$SITE_PATH}/dyn/main/do_upload_attachment">
                            <input id="company_attach_file_input" name="attachment"  type="file" style="display:none" />
                        </form>
                    </div> 
                    <div style="clear:both"> </div> 

                    <div style="text-align: center;margin-top:10px">
                        <a id="save_price_email" style="padding:10px 30px" class="button1 green">{$ns.langManager->getPhraseSpan(43)}</a>         
                        <a id="send_price_email" style="padding:10px 30px" class="button1 orange">{$ns.langManager->getPhraseSpan(48)}</a>         
                    </div>  

                </div>
            </div>



        {/if}
    </div>

    <div class="avo_style_uploadWrapperBox">
        <table cellspacing="0" style="width:100%">
            <thead>
                <tr>
                    <th>{$ns.langManager->getPhraseSpan(60)}</th>
                    <th>{$ns.langManager->getPhraseSpan(69)}</th>
                    <th>{$ns.langManager->getPhraseSpan(70)}</th>
                        {if ($ns.userLevel === $ns.userGroupsAdmin)}
                        <th>{$ns.langManager->getPhraseSpan(71)}</th>
                        {/if}
                </tr>
            </thead>
            <tbody>
                {foreach from=$ns.company_prices item=company_price name=cp}
                    <tr class="{if $smarty.foreach.cp.index % 2 == 1}avo_style_blueTableBox{/if}">
                        <td >{$smarty.foreach.cp.index}</td>
                        <td ><a href="{$SITE_PATH}/price/zipped_price_unzipped/{$company_price->getId()}"> <img src = "{$SITE_PATH}/img/zip_file_download.png"  alt="zip"/> </a></td>

                        <td >{$company_price->getUploadDateTime()}</td>
                        {if ($ns.userLevel === $ns.userGroupsAdmin)}
                            <td ><a id ="company_price_remove_link^{$company_price->getId()}" class="company_price_remove_link_class" href="javascript:void(0);">{$ns.langManager->getPhraseSpan(148)}</a> </td>
                        {/if}
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>

</div>

