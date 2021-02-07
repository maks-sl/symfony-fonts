require('./styles/sort.css');
require('jquery-ui/themes/base/theme.css');

require('jquery-ui/ui/widgets/sortable');
require('jquery-ui/ui/disable-selection');

document.addEventListener('DOMContentLoaded', function () {
    let sortable = $("#sortable");
    sortable.sortable({
        // update: function(event, ui) {
            // console.log("New position: " + ui.item.index());
            // console.log($('#sortable input').serialize());
        // }
    });
    sortable.disableSelection();
});