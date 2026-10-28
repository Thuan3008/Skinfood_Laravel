//$(document).ready(function () {
//    $("#chatBody").append(`<div class="bot">Xin chào! Tôi là trợ lý AI 😊</div>`);
//});

//function toggleChat() {
//    document.getElementById("chatbox").classList.toggle("hidden");
//}

//document.getElementById("chatToggle").onclick = toggleChat;

//function sendMessage() {
//    let msg = $("#userMessage").val().trim();
//    if (!msg) return;

//    $("#chatBody").append(`<div class="user">${msg}</div>`);
//    $("#userMessage").val("");

//    $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);

//    $.post("/Chat/SendMessage", { message: msg }, function (reply) {

//        $("#chatBody").append(`<div class="bot">${reply}</div>`);
//        $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);

//    }).fail(function () {
//        $("#chatBody").append(`<div class="bot">Lỗi kết nối đến server!</div>`);
//    });
//}

//$("#userMessage").keyup(function (event) {
//    if (event.key === "Enter") {
//        sendMessage();
//    }
//});

$(document).ready(function () {
    $("#chatBody").append(`<div class="bot">Xin chào! Tôi là trợ lý AI 😊</div>`);
});

function toggleChat() {
    $("#chatbox").toggleClass("hidden");
}

$("#chatToggle").click(toggleChat);

function sendMessage() {
    let msg = $("#userMessage").val().trim();
    if (!msg) return;

    const userMessage = $("<div>").addClass("user").text(msg);
    $("<div>").addClass("user-wrapper").append(userMessage).appendTo("#chatBody");
    $("#userMessage").val("");
    $("#userMessage").prop("disabled", true);

    $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);

    $.post("/Chat/SendMessage", { message: msg }, function (reply) {
        const botMessage = $("<div>").addClass("bot").text(reply);
        $("<div>").addClass("bot-wrapper").append(botMessage).appendTo("#chatBody");
        $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
    }).fail(function (xhr) {
        const errorText = xhr.responseText || "⚠ Lỗi kết nối server!";
        $("<div>").addClass("bot").text(errorText).appendTo("#chatBody");
    }).always(function () {
        $("#userMessage").prop("disabled", false).trigger("focus");
    });
}

$("#userMessage").keyup(function (event) {
    if (event.key === "Enter") sendMessage();
});
