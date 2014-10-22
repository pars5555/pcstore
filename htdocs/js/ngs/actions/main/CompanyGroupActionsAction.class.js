ngs.CompanyGroupActionsAction = Class.create(ngs.AbstractAction, {
    initialize: function($super, shortCut, ajaxLoader) {
        $super(shortCut, "main", ajaxLoader);
    },
    getUrl: function() {
        return "do_company_group_actions";
    },
    getMethod: function() {
        return "POST";
    },
    beforeAction: function() {
    },
    afterAction: function(transport) {
        var data = transport.responseText.evalJSON();
        if (data.status === "ok") {
            switch (this.params.action)
            {
                case "delete_attachment":
                    break;
            }
        }
        else if (data.status === "err")
        {
            alert(data.message);
        }
    }
});
