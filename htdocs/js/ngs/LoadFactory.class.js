ngs.LoadFactory= Class.create();
ngs.LoadFactory.prototype={
	
	initialize: function(ajaxLoader){
		this.loads = [];
		this.loads["main"] = function temp(){return new ngs.MainLoad("main", ajaxLoader);};
		this.loads["home_page"] = function temp(){return new ngs.HomePageLoad("home_page", ajaxLoader);};
		this.loads["companies_list"] = function temp(){return new ngs.CompaniesListLoad("companies_list", ajaxLoader);};
		this.loads["item_search"] = function temp(){return new ngs.ItemSearchLoad("item_search", ajaxLoader);};
		this.loads["item_warranty"] = function temp(){return new ngs.ItemWarrantyLoad("item_warranty", ajaxLoader);};
		this.loads["pc_configurator"] = function temp(){return new ngs.PcConfiguratorLoad("pc_configurator", ajaxLoader);};
		this.loads["company_profile"] = function temp(){return new ngs.CompanyProfileLoad("company_profile", ajaxLoader);};
		this.loads["service_company_profile"] = function temp(){return new ngs.ServiceCompanyProfileLoad("service_company_profile", ajaxLoader);};
		this.loads["your_profile"] = function temp(){return new ngs.YourProfileLoad("your_profile", ajaxLoader);};
		this.loads["upload_price"] = function temp(){return new ngs.UploadPriceLoad("upload_price", ajaxLoader);};
		this.loads["service_upload_price"] = function temp(){return new ngs.ServiceUploadPriceLoad("service_upload_price", ajaxLoader);};
		this.loads["dealers_list"] = function temp(){return new ngs.DealersListLoad("dealers_list", ajaxLoader);};				
		this.loads["service_dealers_list"] = function temp(){return new ngs.ServiceDealersListLoad("service_dealers_list", ajaxLoader);};				
		this.loads["items_categories"] = function temp(){return new ngs.ItemsCategoriesLoad("items_categories", ajaxLoader);};			
		this.loads["manage_items"] = function temp(){return new ngs.ManageItemsLoad("manage_items", ajaxLoader);};
		this.loads["sub_categories_selection"] = function temp(){return new ngs.SubCategoriesSelectionLoad("sub_categories_selection", ajaxLoader);};
		this.loads["user_management"] = function temp(){return new ngs.UserManagementLoad("user_management", ajaxLoader);};
		this.loads["pending_users_list"] = function temp(){return new ngs.PendingUsersListLoad("pending_users_list", ajaxLoader);};
		this.loads["sub_users_list"] = function temp(){return new ngs.SubUsersListLoad("sub_users_list", ajaxLoader);};
		this.loads["user_registration"] = function temp(){return new ngs.UserRegistrationLoad("user_registration", ajaxLoader);};
		this.loads["contact_us"] = function temp(){return new ngs.ContactUsLoad("contact_us", ajaxLoader);};		
		this.loads["login"] = function temp(){return new ngs.LoginLoad("login", ajaxLoader);};		 
		this.loads["login_dialog"] = function temp(){return new ngs.LoginDialogLoad("login_dialog", ajaxLoader);};		 
		this.loads["forgot_login"] = function temp(){return new ngs.ForgotLoginLoad("forgot_login", ajaxLoader);};		 
		this.loads["your_orders"] = function temp(){return new ngs.YourOrdersLoad("your_orders", ajaxLoader);};
		this.loads["item_large_view"] = function temp(){return new ngs.ItemLargeViewLoad("item_large_view", ajaxLoader);};
		this.loads["footer_links_content"] = function temp(){return new ngs.FooterLinksContentLoad("footer_links_content", ajaxLoader);};
		this.loads["paging"] = function temp(){return new ngs.PagingLoad("paging", ajaxLoader);};
		this.loads["admin_statistics"] = function temp(){return new ngs.AdminStatisticsLoad("admin_statistics", ajaxLoader);};
		this.loads["pcc_select_case"] = function temp(){return new ngs.PccSelectCaseLoad("pcc_select_case", ajaxLoader);};
		this.loads["pcc_select_mb"] = function temp(){return new ngs.PccSelectMbLoad("pcc_select_mb", ajaxLoader);};
		this.loads["pcc_select_ram"] = function temp(){return new ngs.PccSelectRamLoad("pcc_select_ram", ajaxLoader);};
		this.loads["pcc_select_cpu"] = function temp(){return new ngs.PccSelectCpuLoad("pcc_select_cpu", ajaxLoader);};
		this.loads["pcc_select_hdd"] = function temp(){return new ngs.PccSelectHddLoad("pcc_select_hdd", ajaxLoader);};
		this.loads["pcc_select_ssd"] = function temp(){return new ngs.PccSelectSsdLoad("pcc_select_ssd", ajaxLoader);};
		this.loads["pcc_select_cooler"] = function temp(){return new ngs.PccSelectCoolerLoad("pcc_select_cooler", ajaxLoader);};
		this.loads["pcc_select_monitor"] = function temp(){return new ngs.PccSelectMonitorLoad("pcc_select_monitor", ajaxLoader);};
		this.loads["pcc_select_opt"] = function temp(){return new ngs.PccSelectOptLoad("pcc_select_opt", ajaxLoader);};
		this.loads["pcc_select_power"] = function temp(){return new ngs.PccSelectPowerLoad("pcc_select_power", ajaxLoader);};
		this.loads["pcc_select_keyboard"] = function temp(){return new ngs.PccSelectKeyboardLoad("pcc_select_keyboard", ajaxLoader);};
		this.loads["pcc_select_mouse"] = function temp(){return new ngs.PccSelectMouseLoad("pcc_select_mouse", ajaxLoader);};
		this.loads["pcc_select_speaker"] = function temp(){return new ngs.PccSelectSpeakerLoad("pcc_select_speaker", ajaxLoader);};
		this.loads["pcc_select_graphics"] = function temp(){return new ngs.PccSelectGraphicsLoad("pcc_select_graphics", ajaxLoader);};
		this.loads["pcc_item_description"] = function temp(){return new ngs.PccItemDescriptionLoad("pcc_item_description", ajaxLoader);};		
		this.loads["pcc_total_calculations"] = function temp(){return new ngs.PccTotalCalculationsLoad("pcc_total_calculations", ajaxLoader);};
		this.loads["pcc_credit_calculation"] = function temp(){return new ngs.PccCreditCalculationLoad("pcc_credit_calculation", ajaxLoader);};		
		this.loads["shopping_cart"] = function temp(){return new ngs.ShoppingCartLoad("shopping_cart", ajaxLoader);};
		this.loads["cart_step_inner"] = function temp(){return new ngs.CartStepInnerLoad("cart_step_inner", ajaxLoader);};
		this.loads["login_step_inner"] = function temp(){return new ngs.LoginStepInnerLoad("login_step_inner", ajaxLoader);};		
		this.loads["shipping_step_inner"] = function temp(){return new ngs.ShippingStepInnerLoad("shipping_step_inner", ajaxLoader);};
		this.loads["final_step_inner"] = function temp(){return new ngs.FinalStepInnerLoad("final_step_inner", ajaxLoader);};
		this.loads["payment_step_inner"] = function temp(){return new ngs.PaymentStepInnerLoad("payment_step_inner", ajaxLoader);};
		this.loads["confirm_cell_phone_number"] = function temp(){return new ngs.ConfirmCellPhoneNumberLoad("confirm_cell_phone_number", ajaxLoader);};
		this.loads["pcc_auto_configuration_by_filters"] = function temp(){return new ngs.PccAutoConfigurationByFiltersLoad("pcc_auto_configuration_by_filters", ajaxLoader);};
		this.loads["next_24_hours_select"] = function temp(){return new ngs.Next24HoursSelectLoad("next_24_hours_select", ajaxLoader);};
		this.loads["deals"] = function temp(){return new ngs.DealsLoad("deals", ajaxLoader);};
		this.loads["your_mails"] = function temp(){return new ngs.YourMailsLoad("your_mails", ajaxLoader);};
		this.loads["mails_inbox"] = function temp(){return new ngs.InboxLoad("mails_inbox", ajaxLoader);};
		this.loads["mails_sent"] = function temp(){return new ngs.SentLoad("mails_sent", ajaxLoader);};
		this.loads["mails_trash"] = function temp(){return new ngs.TrashLoad("mails_trash", ajaxLoader);};
		this.loads["mails_compose"] = function temp(){return new ngs.ComposeLoad("mails_compose", ajaxLoader);};
		this.loads["mails_email_body"] = function temp(){return new ngs.EmailBodyLoad("mails_email_body", ajaxLoader);};
		this.loads["insert_contact"] = function temp(){return new ngs.InsertContactLoad("insert_contact", ajaxLoader);};							
		this.loads["add_edit_item"] = function temp(){return new ngs.AddEditItemLoad("add_edit_item", ajaxLoader);};							
		this.loads["system_config"] = function temp(){return new ngs.SystemConfigLoad("system_config", ajaxLoader);};							
		this.loads["table_view"] = function temp(){return new ngs.TableViewLoad("table_view", ajaxLoader);};							
		this.loads["admin_actions_view"] = function temp(){return new ngs.AdminActionsViewLoad("admin_actions_view", ajaxLoader);};							
		this.loads["admin_send_sms"] = function temp(){return new ngs.AdminSendSmsLoad("admin_send_sms", ajaxLoader);};							
		this.loads["company_zipped_prices_more"] = function temp(){return new ngs.CompanyZippedPricesMoreLoad("company_zipped_prices_more", ajaxLoader);};
		this.loads["service_company_zipped_prices_more"] = function temp(){return new ngs.ServiceCompanyZippedPricesMoreLoad("service_company_zipped_prices_more", ajaxLoader);};
		this.loads["import_price"] = function temp(){return new ngs.ImportPriceLoad("import_price", ajaxLoader);};
		this.loads["import_step_one"] = function temp(){return new ngs.ImportStepOneLoad("import_step_one", ajaxLoader);};		
		this.loads["import_step_two"] = function temp(){return new ngs.ImportStepTwoLoad("import_step_two", ajaxLoader);};
		this.loads["import_step_three"] = function temp(){return new ngs.ImportStepThreeLoad("import_step_three", ajaxLoader);};
		this.loads["open_newsletter"] = function temp(){return new ngs.OpenNewsletterLoad("open_newsletter", ajaxLoader);};
		this.loads["save_newsletter"] = function temp(){return new ngs.SaveNewsletterLoad("save_newsletter", ajaxLoader);};
		this.loads["manage_newsletters"] = function temp(){return new ngs.ManageNewslettersLoad("manage_newsletters", ajaxLoader);};
		this.loads["gallery_view"] = function temp(){return new ngs.GalleryViewLoad("gallery_view", ajaxLoader);};
		this.loads["search_statistics_view"] = function temp(){return new ngs.SearchStatisticsViewLoad("search_statistics_view", ajaxLoader);};
		this.loads["new_mail_server_dialog"] = function temp(){return new ngs.NewMailServerDialogLoad("new_mail_server_dialog", ajaxLoader);};
		this.loads["admin_user_management"] = function temp(){return new ngs.AdminUserManagementLoad("admin_user_management", ajaxLoader);};
		this.loads["items_management"] = function temp(){return new ngs.ItemsManagementLoad("items_management", ajaxLoader);};
		this.loads["companies_management"] = function temp(){return new ngs.CompaniesManagementLoad("companies_management", ajaxLoader);};
		this.loads["item_pictures"] = function temp(){return new ngs.ItemPicturesLoad("item_pictures", ajaxLoader);};
		this.loads["pcstore_camera_1"] = function temp(){return new ngs.PcstoreCamera1Load("pcstore_camera_1", ajaxLoader);};
		this.loads["newsletter_senders"] = function temp(){return new ngs.NewsletterSendersLoad("newsletter_senders", ajaxLoader);};
		this.loads["create_new_alert"] = function temp(){return new ngs.CreateNewAlertLoad("create_new_alert", ajaxLoader);};
		this.loads["customer_alerts_after_login"] = function temp(){return new ngs.CustomerAlertsAfterLoginLoad("customer_alerts_after_login", ajaxLoader);};
		this.loads["import_google_contacts"] = function temp(){return new ngs.ImportGoogleContactsLoad("import_google_contacts", ajaxLoader);};
        
        
        // ##################### CMS
		this.loads["cms_main"] = function temp(){return new ngs.CmsMainLoad("cms_main", ajaxLoader);};
		this.loads["cms_home"] = function temp(){return new ngs.CmsHomeLoad("cms_home", ajaxLoader);};
		this.loads["cms_login"] = function temp(){return new ngs.CmsLoginLoad("cms_login", ajaxLoader);};
		this.loads["cms_users"] = function temp(){return new ngs.CmsUsersLoad("cms_users", ajaxLoader);};
		this.loads["cms_admins"] = function temp(){return new ngs.CmsAdminsLoad("cms_admins", ajaxLoader);};
		this.loads["cms_companies"] = function temp(){return new ngs.CmsCompaniesLoad("cms_companies", ajaxLoader);};
		this.loads["cms_service_companies"] = function temp(){return new ngs.CmsServiceCompaniesLoad("cms_service_companies", ajaxLoader);};
		this.loads["cms_online_users"] = function temp(){return new ngs.CmsOnlineUsersLoad("cms_online_users", ajaxLoader);};
        
	},
	
	getLoad: function(name){
		try{
			return this.loads[name]();
		}
		catch(ex){
		}
	}
};