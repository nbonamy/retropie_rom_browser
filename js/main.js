$(document).ready(function() {

  $('.game .delete').on('click', function() {

    // save
    var self = $(this);
    var game = $(this).closest('.game');

    // delete
    if (confirm('Are you sure you want to delete this game?')) {
      $.ajax({
        url: 'delete.php?system=' + g_system + '&filename=' + encodeURIComponent(game.data('name')) + '&image=' + encodeURIComponent(game.data('image')),
        success: function(data) {
          game.remove();
        },
        error: function(err) {
          alert('Error while deleting ROM')
        }
      });
    }

  });

  $('.game .favorite').on('click', function() {

    // save
    var self = $(this);
    var game = $(this).closest('.game');

    // favorite
    $.ajax({
      dataType: 'json',
      url: 'favorite.php?system=' + g_system + '&filename=' + encodeURIComponent(game.data('name')),
      success: function(data) {
        if (data.favorite) self.text('♥️').addClass('active');
        else self.text('♡').removeClass('active');
      },
      error: function(err) {
        alert('Error while favoriting ROM')
      }
    });

    // no default
    return false;

  });
});