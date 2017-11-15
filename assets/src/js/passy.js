/*!
 * PASSY 2.x.x
 * Copyright 2017 Sefa Eyeoglu
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

var passy = (function () {
	//##################################################################################################################
	//GLOBAL VARS
	//##################################################################################################################
	var currentPage = "login",
		currentScope = "logged_out",
		switchingPage = false,
		options = {
			fade_on_focus_loss: true
		},
		latestStatus = null,
		snackbarCount = 0,
		spinnerSVG = '<svg class="spinner" width="20px" height="20px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg>';

	//##################################################################################################################
	//GLOBAL METHODS
	//##################################################################################################################

	function request(data, onSuccess, onFailure, options) {
		if (!data)
			return null;

		if (!onFailure) {
			onFailure = function () {
				snackbar("There has been a problem with the server. Please try again later.");
			}
		}

		if (!options)
			options = {};

		options.url = "action.php";
		options.method = "POST";
		options.data = data;
		options.success = function (data, textStatus, jqXHR) {
			if (!data.success) {
				switch (data.msg) {
					case "not_authenticated":
						closeSession();
						return;

					case "database_error":
						snackbar("There has been a problem with the database. Please try again later.");
						return;
				}
			}
			onSuccess(data, textStatus, jqXHR);
		};
		options.error = function (jqXHR, textStatus, errorThrown) {
			onFailure(jqXHR, textStatus, errorThrown);
		};
		return $.ajax(options);
	}

	function snackbar(content, buttonText, buttonCallback, buttonType) {
		var snackbar = $('<div class="snackbar"></div>');

		if (!buttonText) {
			buttonText = "Dismiss";
		}

		const snackbarKill = setTimeout(function () {
			killSnackbar(snackbar);
		}, 5000);

		if (!buttonCallback) {
			buttonCallback = function () {
				clearTimeout(snackbarKill);
				killSnackbar(snackbar);
			};
		}
		if (!buttonType) {
			buttonType = "primary";
		}

		var button = $('<button class="btn btn-' + buttonType + ' btn-flat">' + buttonText + '</button>');
		snackbar.append('<span class="snackbar-text">' + content + '</span>');
		snackbar.append(button);

		button.on("click", buttonCallback);

		$("body").append(snackbar);
		setTimeout(function () {
			snackbar.addClass("snackbar-show");
			snackbar.css({"bottom": "-" + snackbar.outerHeight() + "px"});
			setTimeout(function () {
				const offset = snackbarCount++ * (snackbar.outerHeight() + 10) + 20;
				snackbar.css({"bottom": offset + "px"});
			}, 1);
		}, 1);
	}

	function killSnackbar(elem) {
		elem.removeClass("snackbar-show");
		elem.css({"bottom": "-" + elem.outerHeight() + "px"});
		elem.on('transitionend webkitTransitionEnd oTransitionEnd', function () {
			elem.off('transitionend webkitTransitionEnd oTransitionEnd');
			elem.remove();
			snackbarCount--;
		});
	}

	function hideAllModals() {
		if ($("body").hasClass("modal-open")) {
			$('.modal.fade.in').modal('hide');
		}
	}

	function randomPassword(length) {
		const safeAlphabet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
			specialAlphabet = "@#$%_-";

		var string = "";

		for (var i = 0; i < length; i++) {
			const alphabet = (i === 0 || i === length) ? safeAlphabet : safeAlphabet + specialAlphabet; // first and last letter is not a special char


			string += alphabet.charAt(randomNumber(alphabet.length));
		}
		return string;
	}

	function randomNumber(max = 1) {
		return Math.floor(Math.random() * max)
	}

	function startsWith(haystack, needle) {
		return haystack.substr(0, needle.length) === needle;
	}

	function getInitialPage() {
		var anchor = location.href.substring(location.href.indexOf("#"));
		if (anchor.substring(0, 4) === "#!p=" && anchor.length > 1) {
			return anchor.substring(4);
		}
		return currentPage;
	}

	function loadPage(page, callback, pushHistory) {
		if (switchingPage)
			return;
		switchingPage = true;
		var oldPage = $("#page_" + currentPage), newPage = $("#page_" + page), spinner = $(".load-spinner");
		currentPage = page;
		changePageScope(newPage.attr("data-apply-page-scope"));
		hideAllModals();

		spinner.addClass("shown");

		$("*[data-page-highlight]").each(function (index, element) {
			const elem = $(element);
			if (elem.attr("data-page-highlight") !== page) {
				elem.removeClass("active");
			}
		});

		var show = function () {
			$("*[data-page-highlight]").each(function (index, element) {
				const elem = $(element);
				if (elem.attr("data-page-highlight") === page) {
					elem.addClass("active");
				}
			});
			if (pushHistory !== false)
				history.pushState({"page": currentPage, "scope": currentScope}, "PASSY", "#!p=" + currentPage);
			spinner.removeClass("shown");
			newPage.fadeIn(300);
			switchingPage = false;
			if (!callback)
				callback();
		};

		oldPage.fadeOut(300, function () {
			if (page === "password_list" || page === "archived_password_list") {
				fetchPasswords(show);
			} else if (page === "login_history") {
				fetchIPLog(show);
			} else if (page === "user_settings") {
				fetchStatus(function () {
					if (!latestStatus) {
						// 2fa Status
						const twoFactorEnableButton = $("#btn2faSetupModalToggle"),
							twoFactorDisableButton = $("#btn2faDisableModalToggle"),
							twoFactorStatus = $("#text_2fa_status"),
							passwordChangeForm = $("#page_user_settings_form_change_password"),
							passwordChangeDisabledAlert = $("#page_user_settings_alert_change_password_disabled");

						if (latestStatus.two_factor.enabled) {
							twoFactorStatus.html("Enabled");
							twoFactorStatus.removeClass("text-danger");
							twoFactorStatus.addClass("text-success");

							twoFactorEnableButton.hide();
							twoFactorEnableButton.attr("disabled", "");

							twoFactorDisableButton.show();
							twoFactorDisableButton.attr("disabled", null);

							passwordChangeForm.hide();
							passwordChangeDisabledAlert.show();
						} else {
							twoFactorStatus.html("Disabled");
							twoFactorStatus.removeClass("text-success");
							twoFactorStatus.addClass("text-danger");

							twoFactorEnableButton.show();
							twoFactorEnableButton.attr("disabled", null);

							twoFactorDisableButton.hide();
							twoFactorDisableButton.attr("disabled", "");

							passwordChangeForm.show();
							passwordChangeDisabledAlert.hide();
						}
					}
					show();
				});
			} else {
				show();
			}
		});
	}

	function refresh(callback) {
		loadPage(currentPage, callback, false);
	}

	function changePageScope(scope) {
		currentScope = scope;
		$("*[data-page-scope]").each(function (i, element) {
			const elem = $(element);
			if (elem.data("page-scope") !== scope) {
				elem.hide();
			} else {
				elem.show();
			}
		});
	}

	function doLogout() {
		request("a=user/logout", function () {
			snackbar("You have been logged out!");
			loadPage("login");
		});
	}

	function closeSession() {
		snackbar("Your session has expired!");
		switchingPage = false;
		loadPage("login", function () {
			history.replaceState({"page": currentPage, "scope": currentScope}, "PASSY", "#!p=" + currentPage);
		}, false);
	}

//##################################################################################################################
//DOCUMENT LOAD
//##################################################################################################################
	$(function () {
		currentPage = getInitialPage();
		fetchStatus(function () {
			if (latestStatus.logged_in && currentScope === "logged_out") {
				if (currentPage === "login" || currentPage === "register")
					currentPage = "password_list"; // load password list if already authenticated

			} else if (!latestStatus.logged_in && currentScope === "logged_in") {
				closeSession();
			}

			refresh(function () {
				history.replaceState({"page": currentPage, "scope": currentScope}, "PASSY", "#!p=" + currentPage);
			});
			registerPageListeners();

			// Start status timer.
			setInterval(function () {
				fetchStatus(function () {
					// Session expired
					if (!latestStatus.logged_in && currentScope === "logged_in") {
						closeSession();
					}
				});
			}, 2000);
		});
	});

	function fetchStatus(callback) {
		request("a=status", function (data) {
			if (!data.success) {
				showConnectionErrorModal();
			} else {
				hideConnectionErrorModal();
			}
			latestStatus = data.data;
			if (callback)
				callback(data);
		}, function () {
			showConnectionErrorModal();
		});
	}

	function showConnectionErrorModal() {
		var modal = $("#modal_connection_lost");
		modal.modal("show");
	}

	function hideConnectionErrorModal() {
		var modal = $("#modal_connection_lost");
		modal.modal("hide");
	}

	function registerPageListeners() {
		var passwordTable = $('#tbodyPasswords'),
			archivedPasswordTable = $('#tbodyArchivedPasswords'),
			inputs = $(".text > input"),
			contextMenu = $("#dropdownContextMenu");

		$.waves(".nav > li > a");
		$.waves(".btn:not([disabled])");

		inputs.each(function (index, element) {
			const elem = $(element);
			if (elem.val().length > 0)
				elem.addClass("hastext");
		});

		inputs.change(function () {
			const me = $(this);
			if (me.val().length > 0)
				me.addClass("hastext");
			else
				me.removeClass("hastext");
		});

		$("*[data-random-value]").click(function () {
			var me = $(this),
				target = $(me.data("random-value"));
			target.val(randomPassword(20));
			target.attr("type", "text");
			target.change();
		});

		$("*[data-next='tab']").click(function (e) {
			e.preventDefault();
			var me = $(this);
			$(me.data("target") + ' > .active').next('li').find('a').trigger('click');
		});

		$("*[data-hide]").click(function () {
			var me = $(this);
			$(me.data("hide")).hide();
		});

		$("*[data-show]").click(function () {
			var me = $(this);
			$(me.data("show")).show();
		});

		$("*[data-submit]").click(function () {
			const me = $(this),
				targetForm = $(me.data("submit"));
			targetForm.data("submit-btn", me);
			targetForm.submit();
		});

		var delay = 100;
		$(".dropdown-menu").find("li").each(function (index, element) {
			const elem = $(element);
			elem.css({"animation-delay": delay + "ms"});
			delay += 25;
		});

		$("input[data-search-in]").on("keyup", function () {
			console.log("change");
			var me = $(this),
				query = me.val(),
				target = $(me.attr("data-search-in"));

			if (target.is("table"))
				target = target.find("tbody");


			target.children("tr").each(function (index, child) {
				var elem = $(child);

				const visible = elem.attr("data-visible") === "true";
				const userName = elem.children(0).text();
				const description = elem.children(2).text();

				if ((userName !== "None" && userName.indexOf(query) !== -1) || (description !== "None" && description.indexOf(query) !== -1)) {
					if (!visible) {
						elem.show();
						elem.attr("data-visible", "true");
					}
				} else {
					if (visible) {
						elem.hide();
						elem.attr("data-visible", "false");
					}
				}
			});
		});

		var lastHeight = 0;
		const navbar = $(".navbar-fixed-top");
		$(window).scroll(function () {
			$(".dropdown.open").find(".dropdown-toggle").dropdown("toggle");
			$(".contextmenu.open").removeClass("open");

			var scrollTop = $(document).scrollTop(),
				firstHeight = navbar.children().first().outerHeight();

			if (scrollTop < firstHeight) {
				navbar.css({transform: "translateY(-" + scrollTop + "px)"});
				lastHeight = scrollTop;
			}
			if (scrollTop >= firstHeight) {
				navbar.css({transform: "translateY(-" + firstHeight + "px)"});
			}
		});

		$(window).focus(function () {
			const content = $(".content");
			if (!options.fade_on_focus_loss && !content.hasClass("content-hidden"))
				return;
			content.removeClass("content-hidden");
		}).blur(function () {
			if (!options.fade_on_focus_loss)
				return;
			if ($('iframe:hover').length === 0)
				$(".content").addClass("content-hidden");
		});

		$(document).on("keydown", function (e) {
			if ((e.which || e.keyCode) === 116) {
				e.preventDefault();
				if (e.shiftKey)
					location.reload(true);
				else
					refresh();
			}
		});

		$(document).on("mouseup", function (e) {
			if (e.which === 1)
				contextMenu.removeClass("open");
		});

		$(document).on("contextmenu", function (e) {
			if (e.shiftKey)
				return;
			var x = e.clientX,
				y = e.clientY;
			var elementUnderMouse = $(document.elementFromPoint(x, y));
			if (elementUnderMouse.hasClass("no-contextmenu") || elementUnderMouse.parents(".no-contextmenu").length > 0)
				return;
			if (elementUnderMouse.is("input") || elementUnderMouse.is("textarea") || elementUnderMouse.is("select"))
				return;
			e.preventDefault();
			contextMenu.removeClass("open");
			setTimeout(function () {
				contextMenu.css({transform: "translate(" + x + "px, " + y + "px)"});
				contextMenu.addClass("open");
			}, 10);
		});

		$("*[data-to-page]").click(function (e) {
			var me = $(this),
				targetPage = me.attr("data-to-page");
			e.preventDefault();
			if (targetPage === "refresh")
				targetPage = currentPage;

			loadPage(targetPage);
		});

		$(window).on('popstate', function (e) {
			console.log(e);
			var state = e.originalEvent.state;
			if (currentScope === state.scope)
				loadPage(state.page, null, false);
			else {
				history.replaceState({"page": currentPage, "scope": currentScope}, "PASSY", "#!p=" + currentPage);
				e.preventDefault();
			}
		});

		// FORMS

		$("#page_user_settings_form_import").submit(function (e) {
			e.preventDefault();

			const elem = $(this);

			var data = new FormData();
			data.append("a", "misc/import");
			const withPass = elem.find('input[name="with-pass"]')[0].checked;
			if (withPass) {
				data.append("with-pass", "on");
				data.append("pass", elem.find('input[name="pass"]').val());
			}

			$.each($('#import-file')[0].files, function (i, file) {
				data.append("parse-file", file);
			});
			$.ajax({
				url: 'action.php',
				type: 'POST',
				cache: false,
				contentType: false,
				processData: false,
				data: data,
				success: function (data) {
					if (data.success) {
						if (data.data.imported === 0) {
							snackbar("Nothing has been imported.")
						} else {
							snackbar("The import was successful.")
						}
						setTimeout(
							function () {
								loadPage("password_list");
							}, 800)
					} else {
						snackbar("There has been a problem with the server. Please try again later")
					}
				}
			});
		});

		$("#page_user_settings_form_2fa_setup").submit(function (e) {
			var me = $(this);
			e.preventDefault();
			var modal_content = me.parent().parent().parent().parent().parent();
			var btn = modal_content.find("button");
			btn.attr("disabled", "");
			request(me.serialize(), function (data) {
				btn.attr("disabled", null);
				if (data.success) {
					me[0].reset();
					me.find("input.hastext").change();
					$('#tab_2fa').find('a:last').click();
					refresh();
				}
			})
		});

		$("#page_login_form_login").submit(function (e) {
			var me = $(this);
			e.preventDefault();
			var data = me.serialize();
			me.find("input").attr("disabled", "disabled");
			me.find("button").attr("disabled", "disabled");
			request(data, function (data) {
				var modal = $("#page_login_modal_2fa"),
					reset = true;
				if (data.success) {
					loadPage("password_list");
					modal.find("input").attr("disabled", "");
				} else {
					switch (data.msg) {
						case "already_logged_in":
							loadPage("password_list");
							break;
						case "invalid_credentials":
							snackbar("The entered credentials do not match any account.");
							break;
						case "two_factor_needed":
							modal.find("input").attr("disabled", null);
							modal.modal("show");
							reset = false;
							break;
						case "invalid_code":
							snackbar("The entered two-factor-authentication code is invalid!");
							modal.find("input").attr("disabled", null);
							reset = false;
							break;
					}
				}
				if (reset)
					me[0].reset();
				me.find("input").change();

				me.find("input").not(".modal input").attr("disabled", null);
				me.find("button").attr("disabled", null);
			}, function () {
				snackbar("There has been a problem with the server.");
				me[0].reset();
				me.find("input").change();
				me.find("input").attr("disabled", null);
				me.find("button").attr("disabled", null);
			});
		});

		$("#page_register_form_register").submit(function (e) {
			var me = $(this);
			e.preventDefault();
			var data = me.serialize();
			me.find("input").attr("disabled", "disabled");
			me.find("button").attr("disabled", "disabled");
			request(data, function (data) {
				if (data.success) {
					loadPage("login");
					snackbar("Your account has been created. You may log in.")
				} else {
					switch (data.msg) {
						case "already_logged_in":
							loadPage("password_list");
							break;
						case "passwords_not_matching":
							snackbar("The entered passwords do not match.");
							break;
						case "recaptcha_fail":
							snackbar("Captcha could not be verified.");
							break;
						case "username_exists":
							snackbar("The username is occupied.");
							break;
					}
				}
				me[0].reset();
				me.find("input").change();
				me.find("input").attr("disabled", null);
				me.find("button").attr("disabled", null);

				if (!grecaptcha)
					grecaptcha.reset();
			}, function () {
				snackbar("There has been a problem with the server.");
				me[0].reset();
				me.find("input").change();
				me.find("input").attr("disabled", null);
				me.find("button").attr("disabled", null);

				if (!grecaptcha)
					grecaptcha.reset();
			})
		});

		$("#page_password_list_form_add").submit(function (e) {
			var me = $(this);
			e.preventDefault();
			var btn = me.find("button");
			btn.attr("disabled", "");
			request(me.serialize(), function (data) {
				btn.attr("disabled", null);
				if (data.success) {
					me[0].reset();
					me.find("input.hastext").removeClass("hastext");
					refresh();
					hideAllModals();
				} else {
					snackbar("There has been a problem with the server. Please try again later");
				}
			})
		});

		$("#page_password_list_form_edit").submit(function (e) {
			var me = $(this);
			e.preventDefault();
			var btn = me.find("button");
			btn.attr("disabled", "");
			request(me.serialize(), function (data) {
				btn.attr("disabled", null);
				if (data.success) {
					me[0].reset();
					me.find("input").change();
					refresh();
					hideAllModals();
				} else {
					snackbar("There has been a problem with the server. Please try again later")
				}
			})
		});

		$("#page_user_settings_form_change_password").submit(function (e) {
			var me = $(this);
			e.preventDefault();
			var btn = me.find("button");
			btn.attr("disabled", "");

			request(me.serialize(), function (data) {
				btn.attr("disabled", null);
				if (data.success) {
					me[0].reset();
					me.find("input").change();
					doLogout();
				} else {
					if (data.msg === "invalid_credentials") {
						snackbar("Your current password is not correct.")
					} else if (data.msg === "passwords_not_matching") {
						snackbar("Your new passwords do not match.")
					} else {
						snackbar("There has been a problem with the server. Please try again later")
					}
				}
			});
		});

		$("#page_user_settings_form_2fa_disable").submit(function (e) {
			var me = $(this),
				submitBtn = me.data("submit-btn");
			e.preventDefault();
			submitBtn.attr("disabled", "");

			request(me.serialize(), function (data) {
				submitBtn.attr("disabled", null);
				if (data.success) {
					me[0].reset();
					me.find("input").change();
					snackbar("Two-factor-authentication has been disabled!");
					refresh();
					hideAllModals();
				} else {
					if (data.msg === "invalid_code") {
						snackbar("The entered two-factor-authentication code is invalid!")
					} else {
						snackbar("There has been a problem with the server. Please try again later")
					}
				}
			});
		});

		$("#page_user_settings_form_change_username").submit(function (e) {
			var me = $(this);
			e.preventDefault();
			var btn = me.find("button");
			btn.attr("disabled", "");

			request(me.serialize(), function (data) {
				btn.attr("disabled", null);
				if (data.success) {
					me[0].reset();
					me.find("input").change();
					doLogout();
				} else {
					if (data.msg === "username_exists") {
						snackbar("The username is occupied.")
					} else if (data.msg === "invalid_credentials") {
						snackbar("Your current password is not correct.")
					} else {
						snackbar("There has been a problem with the server. Please try again later")
					}
				}
			});
		});

		$("#btnLogout").click(function (e) {
			e.preventDefault();
			doLogout();
		});

		//PASSWORD ACTIONS
		passwordTable.on('click', '*[data-password-action="show"]', function (e) {
			var me = $(this), passwordId = me.data("password-id"), parent = me.parent();
			e.preventDefault();
			me.attr("disabled", "");
			me.html(spinnerSVG);
			request("a=password/query&id=" + encodeURIComponent(passwordId), function (data) {
				if (data.success) {
					parent.html("<span class='force-select no-contextmenu'>" + data.data.password.safe + "</span>");
					timeoutPassword(parent, passwordId);
				} else {
					me.html("<i class='material-icons'>error</i>");
					snackbar("There has been a problem with the server. Please try again later")
				}
			}, function () {
				me.html("<i class='material-icons'>error</i>");
				snackbar("There has been a problem with the server. Please try again later")
			});
		});

		passwordTable.on('click', '*[data-password-action="edit"]', function (e) {
			var me = $(this), passwordId = me.data("password-id"), targetForm = $("#page_password_list_form_edit");
			e.preventDefault();
			me.attr("disabled", "");
			me.html(spinnerSVG);
			request("a=password/query&id=" + encodeURIComponent(passwordId), function (data) {
				if (data.success) {
					me.html("<i class='material-icons'>edit</i>");
					me.attr("disabled", null);
					targetForm.find("input[name='id']").val(passwordId);
					targetForm.find("input[name='username']").val(data.data.username).change();
					targetForm.find("input[name='password']").val(data.data.password).change();
					targetForm.find("input[name='description']").val(data.data.description).change();
					$("#page_password_list_modal_edit").modal("show");
				} else {
					me.html("<i class='material-icons'>error</i>");
					snackbar("There has been a problem with the server. Please try again later")
				}
			}, function () {
				me.html("<i class='material-icons'>error</i>");
				snackbar("There has been a problem with the server. Please try again later")
			});
		});

		passwordTable.on('click', '*[data-password-action="archive"]', function (e) {
			var me = $(this), passwordId = me.data("password-id");
			e.preventDefault();
			me.attr("disabled", "");
			me.html(spinnerSVG);
			request("a=password/archive&id=" + encodeURIComponent(passwordId), function (data) {
				if (data.success) {
					refresh();
				} else {
					me.html("<i class='material-icons'>error</i>");
					snackbar("There has been a problem with the server. Please try again later")
				}
			}, function () {
				me.html("<i class='material-icons'>error</i>");
				snackbar("There has been a problem with the server. Please try again later")
			});
		});

		archivedPasswordTable.on('click', '*[data-password-action="restore"]', function (e) {
			var me = $(this), passwordId = me.data("password-id");
			e.preventDefault();
			me.attr("disabled", "");
			me.html(spinnerSVG);
			request("a=password/restore&id=" + encodeURIComponent(passwordId), function (data) {
				if (data.success) {
					refresh();
				} else {
					me.html("<i class='material-icons'>error</i>");
					snackbar("There has been a problem with the server. Please try again later")
				}
			}, function () {
				me.html("<i class='material-icons'>error</i>");
				snackbar("There has been a problem with the server. Please try again later")
			});
		});

		archivedPasswordTable.on('click', '*[data-password-action="delete"]', function (e) {
			var me = $(this), passwordId = me.data("password-id");
			e.preventDefault();
			me.attr("disabled", "");
			me.html(spinnerSVG);
			request("a=password/delete&id=" + encodeURIComponent(passwordId), function (data) {
				if (data.success) {
					refresh();
				} else {
					me.html("<i class='material-icons'>error</i>");
					snackbar("There has been a problem with the server. Please try again later")
				}
			}, function () {
				me.html("<i class='material-icons'>error</i>");
				snackbar("There has been a problem with the server. Please try again later")
			});
		});


		// MODALS
		$('.modal').on('shown.bs.modal', function () {
			var me = $(this);
			me.find('input[type=text],textarea,select').filter(':visible:first').focus();
		});

		$('#page_user_settings_modal_2fa_setup').on('show.bs.modal', function () {
			var me = $(this);
			$('#tab_2fa').find('a:first').tab('show');
			me.find("form").each(function (i, elem) {
				elem.reset();
			});
			$("#btn_2fa_next").show();
			$("#btn_2fa_enable_submit").hide();
			$("#btn_2fa_finish").hide();
			request("a=user/2faGenerateKey", function (data) {
				if (data.success) {
					$("#pre_2fa_key").text(data.data.privateKey);
					$("#pre_2fa_key2").text(data.data.privateKey);
					$("#img_2fa_key").attr("src", data.data.qrCodeUrl);
					me.find("input[name='2faPrivateKey']").val(data.data.privateKey).change();
				} else {
					snackbar("There has been a problem with the server. Please try again later")
				}
			});

		})
	} // END registerPageListeners()


//##################################################################################################################
//PAGE SPECIFIC METHODS
//##################################################################################################################

	function timeoutPassword(passwordObject, passwordId) {
		var timeLeft = 60;
		passwordObject.append(" <span id='timeLeft_" + passwordId + "' class='text-muted'></span>");
		var timeLeftDisplay = passwordObject.find("#timeLeft_" + passwordId);

		var timer = function () {
			timeLeftDisplay.html(timeLeft);
			if (timeLeft === 10) {
				timeLeftDisplay.addClass("text-danger");
				timeLeftDisplay.removeClass("text-muted");
			} else if (timeLeft === 0) {
				clearInterval(timerId);
				passwordObject.html("<a class='btn btn-default btn-flat btn-block' data-password-action='show' data-password-id='" + passwordId + "'><i class='material-icons'>remove_red_eye</i></a>");
			}
			timeLeft--;
		};
		timer();

		var timerId = setInterval(timer, 1000);
	}


	function fetchPasswords(callbackDone) {
		var tableBody = $("#tbodyPasswords");
		var tableArchivedBody = $("#tbodyArchivedPasswords");
		request("a=password/queryAll", function (data) {
			var tbody = "",
				tbodyArchived = "";
			if (data.success) {
				$.each(data.data, function (index, item) {

					var description = "<i>None</i>";
					if (item.description.raw)
						description = item.description.safe;

					var username = "<i>None</i>";
					if (item.username.raw)
						username = item.username.safe;

					var row = "<tr data-visible='true' id='" + item.password_id + "'>";
					if (!item.archived) {
						//Passwords page
						row += "<td><span class='force-select no-contextmenu'>" + username + "</span></td>";
						row += "<td><button class='btn btn-default btn-flat btn-block' data-password-action='show' data-password-id='" + item.password_id + "'><i class='material-icons'>remove_red_eye</i></button></td>";
						row += "<td>" + description + "</td>";
						row += "<td>" + item.date_added.pretty + "</td>";
						row += "<td>" +
							"<button class='btn btn-default btn-flat btn-sm' data-password-action='edit' data-password-id='" + item.password_id + "'>" +
							"<i class='material-icons'>edit</i>" +
							"</button>" +
							"<button class='btn btn-default btn-flat btn-sm' data-password-action='archive' data-password-id='" + item.password_id + "'>" +
							"<i class='material-icons'>archive</i>" +
							"</button>" +
							"</td>";
						row += "</tr>";
						tbody += row;
					} else {
						//Archived page
						row += "<td><span class='force-select no-contextmenu'> " + username + "</span></td>";
						row += "<td><button class='btn btn-default btn-flat btn-block' disabled='disabled'><i class='material-icons'>remove_red_eye</i></button></td>";
						row += "<td>" + description + "</td>";
						row += "<td>" + item.date_archived.pretty + "</td>";
						row += "<td><button class='btn btn-default btn-flat btn-sm' data-password-action='restore' data-password-id='" + item.password_id + "'><i class='material-icons'>unarchive</i></button><a class='btn btn-default btn-flat btn-sm' data-password-action='delete' data-password-id='" + item.password_id + "'><i class='material-icons'>delete</i></a></td>";
						row += "</tr>";
						tbodyArchived += row;
					}
				});
			} else {
				snackbar("There has been a problem with the server. Please try again later");
			}
			tableBody.html(tbody);
			tableArchivedBody.html(tbodyArchived);
			if (callbackDone)
				callbackDone(data.msg);
		}, function () {
			tableBody.html("");
			snackbar("There has been a problem with the server. Please try again later");
			if (callbackDone)
				callbackDone(false);
		});

	}

	function fetchIPLog(callbackDone) {
		var tableBody = $("#tbodyLoginHistory");
		request("a=iplog/queryAll", function (data) {
			if (data.success) {
				var jsonData = data.data, tbody = "";
				$.each(jsonData, function (index, item) {
					var row = "<tr>";

					row += "<td><span>" + item.ip + "</span></td>";
					row += "<td><span>" + item.user_agent + "</span></td>";
					row += "<td><span>" + item.date.pretty + "</span></td>";
					row += "</tr>";

					tbody += row;
				});
				tableBody.html(tbody);
				if (callbackDone)
					callbackDone();
			} else {
				if (data.msg === "not_authenticated") {
					closeSession();
					return;
				}

				tableBody.html("");
				snackbar("There has been a problem with the server. Please try again later");
				if (callbackDone)
					callbackDone(data.msg);
			}
		}, function () {
			tableBody.html("");
			snackbar("There has been a problem with the server. Please try again later");
			if (callbackDone)
				callbackDone(false);
		});
	}

	return {
		setOption: function (option, value) {
			var previousValue = options[option];
			options[option] = value;
			return "Previous value was " + previousValue;
		}
	}
})();
