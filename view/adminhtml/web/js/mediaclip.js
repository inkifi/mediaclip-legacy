require([
    'jquery',
], function ($) {
    var status = true;
    var STATUS_SELECTOR = 'input[name="product[media_clip_product]"]';

    var MODULE_SELECTOR = 'select[name="product[mediaclip_module]"]';

    var PHOTOBOOK_PRODUCT_SELECTOR = 'select[name="product[mediaclip_photobook_product]"]';
    var EXTRA_AMT_SELECTOR = 'input[name="product[media_clip_extrasheetamt]"]';

    var GIFTING_PRODUCT_SELECTOR = 'select[name="product[mediaclip_gifting_product]"]';

    var PRINTS_PRODUCT_SELECTOR = 'select[name="product[mediaclip_print_product]"]';
    var PRINTS_MIN_ALLOW_SELECTOR = 'input[name="product[mediaclip_minimum_prints_allow]"]';
    var PRINTS_MIN_COUNT_SELECTOR = 'input[name="product[mediaclip_minimum_prints_count]"]';
    var PRINTS_EXTRA_AMT_SELECTOR = 'input[name="product[mediaclip_extra_prints_price]"]';

    var DUSTJACKET_POPUP_SELECTOR = 'select[name="product[mediaclip_dustjacket_popup]"]';
    var FTP_UPLOAD_FLODER_SELECTOR = 'select[name="product[mediaclip_upload_folder]"]';

    var IS_DUST_JACKET_PRODUCT = 'select[name="product[mediaclip_product_supplier]"]';
    $(document).ready(function(){
        alert('6');
        STATUS_SELECTOR = "input[name='product[media_clip_product]']" ;
        time = 100 ;
        waitForElementToDisplay(PRINTS_MIN_ALLOW_SELECTOR,time);
        if($('input[name="product[mediaclip_minimum_prints_allow]"]').val() == 1){
            checkPrintsMinimum();
        }
    });
    function waitForElementToDisplay(selector, time) {

    
        if(document.querySelector(selector) != null) {
           
            if (status) {

                hideAll();
                $(PRINTS_MIN_ALLOW_SELECTOR).on('change', function(){
                    checkPrintsMinimum();
                });

                status = false;
            }
            return;
        }
        else {
            
            setTimeout(function() {
                waitForElementToDisplay(selector, time);
            }, time);
        }
    }
    function hideAll(){

       
        $(PRINTS_MIN_COUNT_SELECTOR).parent().parent().hide();
        
        $(PRINTS_EXTRA_AMT_SELECTOR).parent().parent().hide();
    }
    
    function checkPrintsMinimum(){
        if ($(PRINTS_MIN_ALLOW_SELECTOR).val() == '0') {
            hidePrintsMinimumFields();
        } else if ($(PRINTS_MIN_ALLOW_SELECTOR).val() == '1') {
            showPrintsMinimumFields();
        }
    }

    function hidePrintsMinimumFields(){    
        $(PRINTS_MIN_COUNT_SELECTOR).parent().parent().hide();
        $(PRINTS_EXTRA_AMT_SELECTOR).parent().parent().hide();
    }

    function showPrintsMinimumFields(){
        $(PRINTS_MIN_COUNT_SELECTOR).parent().parent().show();
        $(PRINTS_EXTRA_AMT_SELECTOR).parent().parent().show();
    }
});