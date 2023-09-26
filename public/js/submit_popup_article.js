$(function() {
    $('.mobile-trigger-type').click(function(event) {
        $('body').toggleClass('mobile-open-type');
        $('custom-menu-primary-type .hs-menu-wrapper-type ul').removeClass('child-open-type after-child-open-type');
    });
    $('.custom-menu-primary-type .hs-menu-wrapper-type > ul').addClass('parent-wrapper-type');
    $('.parent-wrapper-type').after('<div class="dropdown-wrapper-type"></div>');
    $('.custom-menu-primary-type .hs-menu-wrapper-type > ul > li > ul').addClass('first-dropdown-type');
    $('.custom-menu-primary-type .hs-menu-wrapper-type  > ul  li.hs-item-has-children-type').each(function(index, el) {
        var id = $(this).children('a').text();
        var b = $(this).parent().attr('id');
        $(this).children('ul.hs-menu-children-wrapper-type').prepend('<li class="dl-back-type"><a href="' + b + '">Back</a></li>');
        $(this).children('ul.hs-menu-children-wrapper-type').attr('id', 'max-dropdown-type' + index);
        $(this).children('a').attr('href', 'max-dropdown-type' + index);
        var dropdowns = $(this).children('ul.hs-menu-children-wrapper-type');
        $('.dropdown-wrapper-type').append(dropdowns);
    });
    $('.custom-menu-primary-type  .hs-menu-wrapper-type ul li:not(.dl-back-type) a').click(function(event) {
        event.preventDefault();
        var href = $(this).attr('href');
        $('#' + href).addClass('child-open-type').siblings('ul').removeClass('child-open-type');
        $(this).closest('ul').addClass('after-child-open-type');
    });
    $('.dl-back-type').click(function(event) {
        event.preventDefault();
        var a = $(this).children('a').attr('href');
        $(this).parent().removeClass('child-open-type');
        if (!$(this).parent().is('.first-dropdown-type')) {
            $('#' + a).removeClass('after-child-open-type').addClass('child-open-type');
        }
        if ($(this).parent().is('.first-dropdown-type')) {
            $('.parent-wrapper-type').removeClass('after-child-open-type child-open-type')
        }
    });
    jQuery(document).on('click', ".custom-menu-parent-type .custom-menu-primary-type ul li a[role='menuOption']", function(e) {
        e.preventDefault();
        var text = jQuery(this).html();
        var fullname = jQuery(this).attr('data-name');
        var trimStr = $.trim(text);
        $("#issueType").val(trimStr);
        $("#issue_type_id").val(fullname);
        jQuery("body").removeClass("mobile-open-type");
    });
});
$(function() {
    $('.mobile-trigger-component').click(function(event) {
        $('body').toggleClass('mobile-open-component');
        $('custom-menu-primary-component .hs-menu-wrapper-component ul').removeClass('child-open-component after-child-open-component');
    });
    $('.custom-menu-primary-component .hs-menu-wrapper-component > ul').addClass('parent-wrapper-component');
    $('.parent-wrapper-component').after('<div class="dropdown-wrapper-component"></div>');
    $('.custom-menu-primary-component .hs-menu-wrapper-component > ul > li > ul').addClass('first-dropdown-component');
    $('.custom-menu-primary-component .hs-menu-wrapper-component  > ul  li.hs-item-has-children-component').each(function(index, el) {
        var id = $(this).children('a').text();
        var b = $(this).parent().attr('id');
        $(this).children('ul.hs-menu-children-wrapper-component').prepend('<li class="dl-back-component"><a href="' + b + '">Back</a></li>');
        $(this).children('ul.hs-menu-children-wrapper-component').attr('id', 'max-dropdown-component' + index);
        $(this).children('a').attr('href', 'max-dropdown-component' + index);
        var dropdowns = $(this).children('ul.hs-menu-children-wrapper-component');
        $('.dropdown-wrapper-component').append(dropdowns);
    });
    $('.custom-menu-primary-component  .hs-menu-wrapper-component ul li:not(.dl-back-component) a').click(function(event) {
        event.preventDefault();
        var href = $(this).attr('href');
        $('#' + href).addClass('child-open-component').siblings('ul').removeClass('child-open-component');
        $(this).closest('ul').addClass('after-child-open-component');
    });
    $('.dl-back-component').click(function(event) {
        event.preventDefault();
        var a = $(this).children('a').attr('href');
        $(this).parent().removeClass('child-open-component');
        if (!$(this).parent().is('.first-dropdown-component')) {
            $('#' + a).removeClass('after-child-open-component').addClass('child-open-component');
        }
        if ($(this).parent().is('.first-dropdown-component')) {
            $('.parent-wrapper-component').removeClass('after-child-open-component child-open-component')
        }
    });
    jQuery(document).on('click', ".custom-menu-parent-component .custom-menu-primary-component ul li a[role='menuOption']", function(e) {
        e.preventDefault();
        var text = jQuery(this).html();
        var fullname = jQuery(this).attr('data-name');
        var trimStr = $.trim(text);
        $("#productComponent").val(trimStr);
        $("#product_component_id").val(fullname);
        jQuery("body").removeClass("mobile-open-component");
    });
});
$('#submitParameterForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: newPopupUrl,
        type: "POST",
        data: $(this).serialize(),
        success: function(data) {
            var pa = $("#product_area").val();
            var it = $("#issueType").val();
            var pf = $("#pfN").val();
            var pc = $("#product_component_id").val();
            var helpful = $("#helpful").val();
            var submitFormId = $("#submitFormId").val();
            var formType = $("#formType").val();
            var tt_button_id = $("#tt_button_id").val();
            var TilesFId = $("#TilesFId").val();
            if (helpful == "no") {
                $(".helpfulNoSubmitAlready").hide();
                $(".helpfulNoSubmit_" + submitFormId + TilesFId).html('<span style="font-weight: bold;">Product Area:</span><span>' + pa + ',</span><span style="font-weight: bold;">Issue Type:</span><span>' + it + ',</span><span style="font-weight: bold;">Product Family:</span><span>' + pf + ',</span><span style="font-weight: bold;">Product Component:</span><span>' + pc + '</span>');
            }
            if (helpful == "yes") {
                $(".helpfulYesSubmitAlready").hide();
                $(".helpfulYesSubmit_" + submitFormId + TilesFId).html('<span style="font-weight: bold;">Product Area:</span><span>' + pa + ',</span><span style="font-weight: bold;">Issue Type:</span><span>' + it + ',</span><span style="font-weight: bold;">Product Family:</span><span>' + pf + ',</span><span style="font-weight: bold;">Product Component:</span><span>' + pc + '</span>');
            } else if (formType == "tt_form") {
                $(".helpfulYesSubmitAlready").hide();
                $(".helpfulNoSubmitAlready").hide();
                $(".helpfulOtherSubmit" + submitFormId + "555" + tt_button_id).html('<span style="font-weight: bold;">Product Area:</span><span>' + pa + ',</span><span style="font-weight: bold;">Issue Type:</span><span>' + it + ',</span><span style="font-weight: bold;">Product Family:</span><span>' + pf + ',</span><span style="font-weight: bold;">Product Component:</span><span>' + pc + '</span>');
            } else {
                $(".helpfulYesSubmitAlready").hide();
                $(".helpfulNoSubmitAlready").hide();
                $(".helpfulOtherSubmit" + submitFormId).html('<span style="font-weight: bold;">Product Area:</span><span>' + pa + ',</span><span style="font-weight: bold;">Issue Type:</span><span>' + it + ',</span><span style="font-weight: bold;">Product Family:</span><span>' + pf + ',</span><span style="font-weight: bold;">Product Component:</span><span>' + pc + '</span>');
            }
            $("#submitParameters").modal('hide');
        }
    });
});
$(function() {
    $("#issueType").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: issueTypeSearchURL,
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        select: function(event, ui) {
            $("#issue_type_id").val(ui.item.label);
            jQuery("body").removeClass("mobile-open-type");
        }
    }).data("ui-autocomplete")._renderItem = function(ul, item) {
        $(ul).addClass("autoOwnCSSAgent");
        var t = String(item.value).replace(new RegExp(this.term, "gi"), "<span class='ui-state-highlight'>$&</span>");
        return $("<li></li>").data("item.autocomplete", item).append("<a data-leadto='" + item.label + "' >" + t + "</a>").appendTo(ul);
    };
    $("#productComponent").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: productComponentSearchURL,
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        select: function(event, ui) {
            $("#product_component_id").val(ui.item.label);
            jQuery("body").removeClass("mobile-open-component");
        }
    }).data("ui-autocomplete")._renderItem = function(ul, item) {
        $(ul).addClass("autoOwnCSSAgent");
        var t = String(item.value).replace(new RegExp(this.term, "gi"), "<span class='ui-state-highlight'>$&</span>");
        return $("<li></li>").data("item.autocomplete", item).append("<a data-leadto='" + item.label + "' >" + t + "</a>").appendTo(ul);
    };
    $('#product_family').on('change', function(e) {
        var selectedText = $(this).find("option:selected").text();
        $("#pfN").val(selectedText);
    });
});