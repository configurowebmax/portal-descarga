// Datatable function
$(document).ready(function () {
    $('#fileTable').DataTable();
});

// Updating file
function updateFile(id) {
    $("#updateFileModal").modal("show");

    let updateFileID = $("#fileID-" + id).text();
    let updateFileTitle = $("#fileTitle-" + id).text();
    let updateFile = $("#file-" + id).text();
    let updateFileUploader = $("#fileUploader-" + id).text();

    $("#updateFileID").val(updateFileID);
    $("#updateFileTitle").val(updateFileTitle);
    $("#updateFile").text(updateFile);
    $("#updateFileUploader").val(updateFileUploader);
}

// Deleting file
function deleteFile(id) {
    if (confirm("Deseas eliminar este archivo?")) {
        window.location = "./endpoint/delete-file.php?file=" + id;
    }
}

// Download the file
function downloadFile(fileID) {
    window.location.href = './endpoint/download-file.php?fileID=' + fileID;
}

