$(function () {
   
    
    $('.user-row').dblclick(function (e) {
        e.preventDefault();

        var userId = $(this).data("user-id");

        window.location = "index.php?page=user/userdetail&task=edit&id=" + userId;
    });

    $("#editusername").change(function (e) {
        if($(this).val() !== "") {
            $('#editusername').css('border', '1px inset rgb(118, 118, 118)');
        }
    });

     $("#editpassword").change(function (e) {
        if($(this).val() !== "") {
            $('#editpassword').css('border', '1px inset rgb(118, 118, 118)');
        }
    });

    $('#AddUserButton').click(function (e) {
        e.preventDefault();

        window.location = "index.php?page=user/userdetail&task=add";
    });

    $('#SaveButton').click(function (e) {
        e.preventDefault();

        if($.trim($('#editusername').val()) === ''){
            $('#editusername').css('border', '1px solid red');
            return; 
        }

        // Password required for new users, for existing users the password can be empty so the current password will not be changed.
        if($("#task").val() === "add" && $.trim($('#editpassword').val()) === ''){
            $('#editpassword').css('border', '1px solid red');
            return; 
        }

        var id = $('#editid').val();
        var username = $('#editusername').val();
        var password = $('#editpassword').val(); 
        var email = $('#editemail').val(); 
        var level = $('#editlevel').val(); 
        var active = $('#editactive').val();
        var isresource = $('#isresource').val();  

        if ($("#task").val() === "add") {
            AddUser(username, password, email, level, active, isresource);
        }
        else if ($("#task").val() === "edit") {
            EditUser(id, username, password, email, level, active, isresource);
        }
    });

    $('#CancelButton').click(function (e) {
        e.preventDefault();

        window.location = "index.php?page=user/users";
    });

    $('#userTable .user-row').each(function () {
        var userElement = $(this);
        var userId = parseInt($(this).data('user-id'));

        $(this).find('.delete').click(function () {
            if (confirm("Weet u zeker dat u deze gebruiker wilt verwijderen?")) {
                RemoveUser(userId);
                userElement.remove();
            }
        });
    });
});

function AddUser(username, password, email, level, active, isresource) {
    var dataString = 'task=add&username=' + username + '&password=' + password + '&email=' + email + '&level=' + level + '&active=' + active + '&isresource=' + isresource;
    $.ajax({  
        type: "POST",  
        url: "pages/user/process-user.php",  
        data: dataString, 
        success: function(result) { 
            window.location.href = "index.php?page=user/users";
        },
        error: function (xhr, ajaxOptions, thrownError) {
            var error = (xhr.status);
            error = error + ' ' + thrownError;
            $('#errorbox').html(error);
        }  
    });
}

function EditUser(id, username, password, email, level, active, isresource) {
     var dataString = 'task=edit&id=' + id + '&username=' + username + '&password=' + password + '&email=' + email + '&level=' + level + '&active=' + active + '&isresource=' + isresource;
    $.ajax({  
        type: "POST",  
        url: "pages/user/process-user.php",  
        data: dataString, 
        success: function(result) { 
            window.location.href = "index.php?page=user/users";
        },
        error: function (xhr, ajaxOptions, thrownError) {
            var error = (xhr.status);
            error = error + ' ' + thrownError;
            $('#errorbox').html(error);
        }  
    });
}

function RemoveUser(id) {
    var dataString = 'task=delete&id=' + id;
    $.ajax({  
        type: "POST",  
        url: "pages/user/process-user.php",  
        data: dataString,  
        error: function (xhr, ajaxOptions, thrownError) {
            var error = (xhr.status);
            error = error + ' ' + thrownError;
            $('#errorbox').html(error);
        }  
    });
}

