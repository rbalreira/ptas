$(document).ready(function () {

  $("#sidebar_filters").mCustomScrollbar({
    theme: "minimal"
  });

  $("#sidebar_legend").mCustomScrollbar({
    theme: "minimal"
  });

  function collapseLeft(id, navid) {
    $('#' + navid).toggleClass('active');
    var icon = $('#' + id).parent().find(".fas");
    if (icon.hasClass('fa-caret-left')) {
      icon.removeClass('fa-caret-left').addClass("fa-caret-right");
      setTimeout(function () {
        $('#' + navid).css("display", "none");
      }, 200);
    } else {
      icon.removeClass('fa-caret-right').addClass("fa-caret-left");
      $('#' + navid).css("display", "block");
    }
  }

  $(document).on('click', ".select_sidebarCollapse", function () {
    if(this.id === 'sidebarCollapse_filters') collapseLeft(this.id, 'sidebar_filters');
    else collapseLeft(this.id, 'sidebar_legend');
  });

});