"use strict";

function notify()
{
  var status = $('#status');
  var txt = status.text().trim();

  if (txt != '')
  {
    if (txt.search(/^Fehler:.*/) != -1) {
      status.addClass('statusRed');
      status.removeClass('statusGreen');
    } else {
      status.addClass('statusGreen');
      status.removeClass('statusRed');
    }

    status.slideDown('fast', function() {
      status.delay(2000).fadeOut('fast', function() {
        status.html('');
      });
    });
  }
}

var pageChangeWatcher = {
  lastSavedText: '',

  init: function() {
    if ($('#data').length) {
      $('a').on('click', pageChangeWatcher.changeAttempt);
      $('#revertButton').on('click', function(ev) { // Revert Button
        $('#data').val(pageChangeWatcher.getLastSavedText());
      });
      pageChangeWatcher.update();
    }
  },

  update: function() {
    pageChangeWatcher.lastSavedText = $('#data').val();
  },

  getLastSavedText: function()
  {
    return pageChangeWatcher.lastSavedText;
  },

  changeAttempt: function(ev)
  {
    var link = $(this);
    var curText = $('#data').val();

    if (
           (link.attr('id') != 'saveButton')
        && (link.attr('id') != 'revertButton')
        && (!link.hasClass('tbButton'))
       )
    {
      if (pageChangeWatcher.lastSavedText != curText) {
        var yes = confirm('Es gibt ungesicherte Änderungen am Text. Seite wirklich ohne Speichern verlassen?');
        if (!yes) {
          return false;
        }
      }
    }
  }
}


/* Initialisieren
   ________________________________________________________________
 */
$(document).ready(function()
{
  notify();
  pageChangeWatcher.init();

  // Get our entry point
  var ep = $('#ep').text();

  // Gerenderten Text und Hilfe laden
  var article = $('#saveButton').attr('data-article');
  $('#preview').load(ep+'?ajax=ajax&hook=preview&article='+article);

  // Buttons mit Klick-Effekt aufhübschen - ausser Speichern und Menü
  $('.cButton, .styleButton').on('click', function() {
    var but = $(this).attr('id');
    if ((but != 'nowPlaying') && (but != 'saveButton'))
    {
      var cl = $(this).hasClass('cButton') ? 'cButtonPressed' : 'styleButtonPressed';
      var self = $(this);
      $(this).addClass(cl);
      window.setTimeout(function() {
        self.removeClass(cl);
      }, 100);
    }
  });

  // Menü installieren
  $('a#nowPlaying').on('click', function() {
    $(this).toggleClass('cButtonPressed');
    $('#blockMenu').toggle();
  });

  // Remove button
  $('.rmvButton').on('click', function() {
    var check = confirm("Wirklich löschen?");
    if (check == false) {
      return false;
    }
  });

  // Setze Ajax Save
  $('#saveButton').on('click', function(ev)
  {
    var article = $(this).attr('data-article');
    var finalUrl = ep+'?ajax=ajax&hook=text&op=save&article='+article;
    var data = $('#data').val();

    $('#saveButton').toggleClass('cButtonPressed');

    $.post(finalUrl, {'data' : data}, function(resp) {
      var jsn = $.parseJSON(resp);
      $('#saveButton').toggleClass('cButtonPressed');
      $('#status').html(jsn.status);
      notify();
      pageChangeWatcher.update();
      $('#preview').load(ep+'?ajax=ajax&hook=preview&article='+article);
    });
  });

  // Toolbar Buttons - Tag Buttons
  $(".tagButton").on('click', function(ev) {
    var data = $(ev.currentTarget).text();
    $('#data').insertAtCaret('[' + data + '][/' + data + '] ');
    ev.preventDefault();
    return false;
  });

  // Toolbar Buttons - Style Buttons
  $(".styleButton").on('click', function(ev) {
    var data = $(ev.currentTarget).text();
    $('#data').insertAtCaret('.'+data);
    ev.preventDefault();
    return false;
  });

  // Toolbar Buttons - Emoticons
  $(".emoticonButton").on('click', function(ev) {
    var data = $(ev.currentTarget).attr('title');
    $('#data').insertAtCaret(' '+data);
    ev.preventDefault();
    return false;
  });

  // Style Buttons Hovering - toggle help
  $(".toolbar .tagButton, .toolbar .styleButton, .toolbar #designHintButton").hover(function(ev) {
    var tagName = $(ev.currentTarget).text();
    if (tagName == 'Textgestaltung') { tagName = 'css'; }
    $('#preview').hide();
    $('#help').show();
    $('#help .doc').hide();
    $('#help .' + tagName).show();
  }, function(ev){
    $('#preview').show();
    $('#help').hide();
  });

  $("#data").hover(function(ev) {
    $('#help').hide();
    $('#preview').show();
  });

  // Hotkeys einrichten
  shortcut.add("Ctrl+S", function() {
    $('#saveButton').click();
  });

  shortcut.add("Esc", function() {
    if ($('#nowPlaying').hasClass('cButtonPressed') == true)
    {
      $('#nowPlaying').removeClass('cButtonPressed');
      $('#blockMenu').hide();
    }
  });

  // Description Editor
  $('textarea.descEditor').on('blur', function(ev) {
    var text = encodeURIComponent($(this).val());
    var article = $(this).attr('data-article');
    var file = $(this).attr('data-file');
    $.getJSON(ep+'?hook=files&op=descEditor&ajax=ajax&file='+file+'&desc='+text+'&article='+article, function(data) {
      $('#status').html(data.status);
      notify();
    });
  });

  // Filename Editor
  $('input.fnameEditor').on('blur', function(ev) {
    var that = $(this);
    var text = encodeURIComponent(that.val());
    var article = that.attr('data-article');
    var file = that.attr('data-file');
    $.getJSON(ep+'?hook=files&op=fnameEditor&ajax=ajax&file='+file+'&newname='+text+'&article='+article, function(data) {
      that.val(data.saneName);
      that.parent().find('textarea.descEditor').attr('data-file', data.saneName);
      $('#status').html(data.status);
      notify();
    });
  });

  // adminComment Editor
  $('textarea.adminCommentEditor').on('blur', function(ev) {
    var text = encodeURIComponent($(this).val());
    var article = $(this).attr('data-article');
    var which = $(this).attr('data-which');
    $.getJSON(ep+'?hook=comments&op=adminCommentEdit&ajax=ajax&which='+which+'&text='+text+'&article='+article, function(data) {
      $('#status').html(data.status);
      notify();
    });
  });

  // Settings
  $('.pref').on('change', function(ev) {
    var target = $(ev.target);
    var type = target.attr('type');
    var id = target.attr('id');
    var val = undefined;
    var err = false;
    var article = $('#prefFormArticle').text();

    if (type == 'checkbox') {
      val = target.prop("checked");
    } else {
      val = target.prop('value');
      console.log(val);
    }

    if (err == false) {
      $.getJSON(ep+'?ajax=ajax&hook=prefs&op=setPref&article='+article+'&prefKey='+id+'&prefVal='+encodeURIComponent(val), function(data) {
        $('#status').html(data.status);
        notify();
      });
    }
  });

  // Resize handler - FIXME: replace with flex layout, grid or something...
  /*
  $(window).resize(function()
  {
    var mt = $('#mainToolbar').height();
    var data = $('#data').height();
    var content = $('#contentBox').height();
    var footer = $('#footer').height();
    var doc = document.documentElement.clientHeight;
    var nHeight = doc - (content - data) - mt - footer - 70;
    $("#data").height(nHeight);
    $("#infoBox").height(nHeight);
  });
  $(window).resize();
  */

});

