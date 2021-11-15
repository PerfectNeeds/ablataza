/* Contact Form Script */

(function() {

    "use strict";

    var contactForm = {
        initialized: false,
        initialize: function() {

            if (this.initialized)
                return;
            this.initialized = true;

            this.build();
            this.events();

        },
        build: function() {

            this.validations();

        },
        events: function() {



        },
        validations: function() {

            var contactform = $("#contact-form, #login, #register, #recipe-request-form"),
                    url = contactform.attr("action");

            contactform.validate({
                submitHandler: function(form) {

                    // Loading State
                    var submitButton = $(this.submitButton);
                    submitButton.button("loading");

                    // Ajax Submit
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            "name": $("#contact-form #name").val(),
                            "lastName": $("#contact-form #lastName").val(),
                            "password": $("#contact-form #password").val(),
                            "rePassword": $("#contact-form #rePassword").val(),
                            "nPassword": $("#register #nPassword").val(),
                            "renPassword": $("#register #renPassword").val(),
                            "email": $("#contact-form #email").val(),
                            "mobile": $("#contact-form #mobile").val(),
                            "subject": $("#contact-form #subject").val(),
                            "message": $("#contact-form #message").val(),
                            "rating": $("#register #rating").val(),
                            "title": $("#recipe-request-form #fName").val()
                        },
                        dataType: "json",
                        success: function(data) {
                            if (data.response == "success") {

                                $("#contact-alert-success").removeClass("hidden");
                                $("#contact-alert-error").addClass("hidden");

                                // Reset Form
                                $("#contact-form .form-control")
                                        .val("")
                                        .blur()
                                        .parent()
                                        .removeClass("has-success")
                                        .removeClass("has-error")
                                        .find("label.error")
                                        .remove();

                                if (($("#contact-alert-success").position().top - 80) < $(window).scrollTop()) {
                                    $("html, body").animate({
                                        scrollTop: $("#contact-alert-success").offset().top - 80
                                    }, 300);
                                }

                            } else {

                                $("#contact-alert-error").removeClass("hidden");
                                $("#contact-alert-success").addClass("hidden");

                                if (($("#contact-alert-error").position().top - 80) < $(window).scrollTop()) {
                                    $("html, body").animate({
                                        scrollTop: $("#contact-alert-error").offset().top - 80
                                    }, 300);
                                }

                            }
                        },
                        complete: function() {
                            submitButton.button("reset");
                        }
                    });
                },
                rules: {
                    name: {
                        required: true
                    },
                    lastName: {
                        required: true
                    },
                    password: {
                        required: true
                    },
                    rePassword: {
                        required: true,
                         equalTo: "#password"
                    },
                    rating: {
                        required: true
                    },
                    title: {
                        required: true
                    },
                    nPassword: {
                        required: true
                    },
                    renPassword: {
                        required: true,
                         equalTo: "#nPassword"
                    },
                    n_password: {
                        required: true
                    },
                    r_password: {
                        required: true,
                         equalTo: "#nPassword"
                    },
                    email: {
                        required: true,
                        email: true
                    },
//                    mobile: {
//                        required: true
//                    },
                    subject: {
                        required: true
                    },
                    message: {
                        required: true
                    }
                },
                highlight: function(element) {
                    $(element)
                            .parent()
                            .removeClass("has-success")
                            .addClass("has-error");
                },
                success: function(element) {
                    $(element)
                            .parent()
                            .removeClass("has-error")
                            .addClass("has-success")
                            .find("label.error")
                            .remove();
                }
            });

        }

    };

    contactForm.initialize();

})();