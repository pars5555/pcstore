ngs.LanguageManager = {
    getPhrase: function(phraseFormula, langCode) {
        var ul = jQuery.cookie('ul');
        if (typeof langCode !== "undefined")
        {
            ul = langCode;
        }
        if (typeof ul === 'undefined' || (ul !== 'en' && ul !== 'ru' && ul !== 'am'))
        {
            ul = 'en';
        }
        if (typeof phraseFormula === 'undefined') {
            return '';
        }
        phraseFormula = phraseFormula + '';

        if (phraseFormula.indexOf('`') !== -1) {
            var text = phraseFormula.replace(/`(\d+)`/g, function() {
                var pId = arguments[1];
                var varName = 'phrase_' + ul;
                if (ALL_PHRASES[pId][varName].length > 0) {
                    return ALL_PHRASES[pId][varName];
                } else
                {
                    return ALL_PHRASES[pId]['phrase_en'];
                }
            });
            return text;
        } else {
            var pId = phraseFormula;
            var varName = 'phrase_' + ul;
            var ret = pId;
            if (typeof ALL_PHRASES[phraseFormula] !== 'undefined') {
                if (ALL_PHRASES[pId][varName].length > 0) {
                    ret = ALL_PHRASES[pId][varName];
                } else
                {
                    ret = ALL_PHRASES[pId]['phrase_en'];
                }
            }
            return ret;
        }
    },
    getPhraseSpan: function(phraseFormula, langCode) {        
        return "<span class='replaceable_lang_element' phrase_id='" + phraseFormula + "'>" + this.getPhrase(phraseFormula, langCode) + "</span>";
    },    
    changeSiteLanguage: function(langCode) {
        jQuery('.replaceable_lang_element').each(function() {
            var pid = jQuery(this).attr('phrase_id');
            if (typeof pid !== 'undefined') {
                jQuery(this).html(ngs.LanguageManager.getPhrase(pid, langCode));
            }
        });
        jQuery('.translatable_element').each(function() {
            var pid = jQuery(this).attr('phrase_id');
            if (typeof pid !== 'undefined') {
                jQuery(this).html(ngs.LanguageManager.getPhrase(pid, langCode));
            }
        });

        jQuery('.translatable_attribute_element').each(function() {
            var pid = jQuery(this).attr('attribute_phrase_id');
            if (typeof pid !== 'undefined') {
                jQuery(this).attr(jQuery(this).attr('attribute_name_to_translate'), ngs.LanguageManager.getPhrase(pid, langCode));
            }
        });
        jQuery('.translatable_search_content').each(function() {
            var pid = jQuery(this).attr('phrase_id');
            if (typeof pid !== 'undefined') {
                var old_phrase = ngs.LanguageManager.getPhrase(pid, jQuery.cookie('ul'));
                var phrase = ngs.LanguageManager.getPhrase(pid, langCode);
                jQuery(this).find("*").each(function() {
                    if (jQuery(this).html() === old_phrase)
                    {
                        jQuery(this).html(phrase);
                    }
                });

            }
        });

    }
};