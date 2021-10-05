function pluginHandler() {

	let checkAll = document.getElementById("checkAll");

	if (!checkAll) {
		return;
	}

	let setChanges = document.getElementById("setChanges");
	// set the span text with the selected categories
	let multipleOptionsSelect = document.querySelector(".multiple-select");



	//  adding the event listeners 
	checkAll.addEventListener("change", checkАllInputs);
	setChanges.addEventListener("click", handleColumnValues);
	multipleOptionsSelect.addEventListener("click", showSelectedOptions);

	sortTable();

	//  Functions inside pluginHandler function

	function handleColumnValues(event) {
		//   disable the button
		let trigger = event.currentTarget;

		// the input text with values
		let changeValues = parseFloat(
			document.getElementById("changeValues").value
		);
		let changedValues = 0;
		// options for calc
		let calculationVal = document.getElementById("calculation").value;
		let choiceColumn = document.getElementById("choiceColumn");
		let changingElements = document.getElementsByClassName("change-price");
		let elementsToChange = "";
		let numberCheckedBoxes = 0;
		const roundValuesCheck = document.getElementById("roundValuesCheck").checked;
		const priceToPromo = document.getElementById("priceToPromo");


		// check if input with price is empty
		if (isNaN(changeValues) || changeValues < 0 || changeValues > 2500) {
			alert("Въведете валидна сума");
			return;
		}

		//   if the input contains "," - convert it into "."
		if (changeValues.toString().includes(",")) {
			changeValues.replace(",", ".");
		}



		//  loading array with categories to change and adding them into JSON array
		let choiceColumnValsJson = [];
		let options = choiceColumn.options;
		let allPricesPromosChecked = false; // using this to check if all prices checked and price is "0";

		for (let i = 1; i < options.length; i++) {
			//   if all prices should loop
			if (i == 1 && options[i].selected == true) {
				let br = 0;
				for (let option of options) {
					// check if all price columns
					if (br > 2 && br < 18) {
						allPricesPromosChecked = true;
						let dataChange = option.getAttribute("data-change");
						let feed = { value: option.value, dataChange: dataChange };
						choiceColumnValsJson.push(feed);
					}
					br++;
				}
			}
			if (i === 2 && options[i].selected == true) {
				let br = 0;
				for (let option of options) {
					// check if all promo columns
					if (br > 17) {
						allPricesPromosChecked = true;
						let dataChange = option.getAttribute("data-change");
						let feed = { value: option.value, dataChange: dataChange };
						choiceColumnValsJson.push(feed);
					}
					br++;
				}
			}
			if (i > 2 && options[i].selected == true) {
				// check if sigle column is checked
				let dataChange = options[i].getAttribute("data-change");
				let feed = { value: options[i].value, dataChange: dataChange };
				choiceColumnValsJson.push(feed);
			}
		}

		// fill array to loop the categories
		let choiceColumnVals = [];

		for (let feed of choiceColumnValsJson) {
			choiceColumnVals.push(feed.value);
		}

		let number = 0; // set this variable to check the row of JSON array
		for (let choiceColumnVal of choiceColumnVals) {
			//   check the row in JSON array
			let choiceColChange = choiceColumnValsJson[number].dataChange;
			number++;


			// loop all <td> and check if need to change the value of them /if row  are checked(selected) /
			for (let changingElement of changingElements) {
				let changingElementRow = changingElement.closest("tr");
				let checkBoxChecked = changingElementRow.querySelector(".chkbox").checked;

				// check if the row should be changed
				if (checkBoxChecked) {
					numberCheckedBoxes++;


					//   select the columns which should be checked
					elementsToChange = changingElementRow.querySelectorAll(
						"[data-change='" + choiceColChange + "']"
					);



					let currentElement = elementsToChange[choiceColumnVal];
					let currentElementVal = 0;
					// check if the <td> has been already changed with the previous loops
					if (!currentElement.hasAttribute("changed")) {
						if (priceToPromo.checked) {

							const promoDataCol = currentElement.getAttribute("data-col");
							const priceToGet = currentElement.closest("tr").querySelector(`[data-change="price"][data-col="${promoDataCol}"]`);


							currentElementVal = parseFloat(priceToGet.value);


						} else {
							currentElementVal = parseFloat(currentElement.value);
						}

						let changeVal = parseFloat(changeValues);
						// calculations
						switch (calculationVal) {
							case "+":
								changedValues = currentElementVal + changeVal;
								break;
							case "-":
								changedValues = currentElementVal - changeVal;
								break;
							case "=":
								changedValues = changeVal;
								break;

							case "+%":
								changedValues =
									currentElementVal + (currentElementVal * changeVal) / 100;
								break;

							case "-%":
								changedValues =
									currentElementVal - (currentElementVal * changeVal) / 100;
								break;

							default:
						}

						if (changedValues < 0 && currentElementVal != 0) {
							alert(
								"Има отрицателна стойност във въведените промени, направете промяната отново и проверете дали сте въвели правилна сума"
							);
							reloadPage();
							return;
						}

						// change the value of <td> and show the span with the previous price
						if (currentElement.value == 0 && allPricesPromosChecked) {
							continue;
						}

						// check if the variable round is checked 
						if (roundValuesCheck) {
							changedValues = Math.round(changedValues);
						}
						currentElement.setAttribute("changed", "true");
						let currentParent = currentElement.parentElement;
						currentParent.classList.add("bg-green");
						currentParent.querySelector("span").classList.remove("hidden");

						// currentElement.value = changedValues.toFixed(2);
						currentElement.setAttribute("value", changedValues.toFixed(2));

					}
				}
			}

		}

		if (numberCheckedBoxes === 0) {
			alert("Не сте чекнали нито един елемент/колона");
			return;
		}

		trigger.removeEventListener("click", handleColumnValues);
		trigger.classList.add("disabled-element");

		setTimeout(() => {
			trigger.addEventListener("click", handleColumnValues);
			trigger.classList.remove("disabled-element");
		}, 5000);

		//   remove the attribues changed from which were set in the loop to check if <td> has been changed
		for (let changingElement of changingElements) {
			if (changingElement.hasAttribute("changed"))
				changingElement.removeAttribute("changed");
		}
	}

	function showSelectedOptions() {
		let multipleOptions = [];
		for (let option of multipleOptionsSelect) {
			if (option.selected) {
				multipleOptions.push(" " + option.innerText);
			}
		}
		document.getElementById("selectedElements").innerHTML =
			"Избрани: <br>" + multipleOptions;
	}


	function checkАllInputs(trigger) {
		let checkBoxes = document.getElementsByClassName("chkbox");
		let triggerChecked = trigger.currentTarget.checked;
		for (let key in checkBoxes) {
			if (key > 0) {
				checkBoxes[key].checked = triggerChecked;
			}
		}
	}

	function reloadPage() {
		location.reload();
	}

	//  order rows by first column
	function sortTable() {
		let rows = document.getElementsByTagName("tr");
		let currentTable = document.getElementById("pluginTable");
		let newTable = document.createElement("table");
		newTable.classList.add("newTable");
		let allVals = [];


		for (let i = 1; i < rows.length; i++) {
			if (rows[i].querySelector(".frame_number") != null) {
				allVals.push(rows[i].querySelector(".frame_number").value);
			}
		}

		let sortedVals = allVals.sort((a, b) => a - b);
		for (let value of sortedVals) {
			for (let key in rows) {
				if (key > 0) {
					let firstCol = rows[key].querySelector(".frame_number");
					if (firstCol != null) {
						let valueFirstCol = firstCol.value;
						if (value === valueFirstCol) {
							let currentRow = firstCol.closest("tr");
							newTable.appendChild(currentRow);

						}
					}
				}
			}
		}

		currentTable.innerHTML += newTable.innerHTML;
		//  /.  EOF order rows by first column

		// add event Listener again to check all boxes after new rows have been set to the html 
		document.getElementById("checkAll").addEventListener("change", checkАllInputs);

	}



	const documentBody = document.getElementById("wpbody-content");
	const topBar = document.querySelector(".top-bar-plugin");
	const pluginTable = document.getElementById("pluginTable");
	const showOptionsButton = document.querySelector(".show-options-button");
	const messageContainer = document.querySelector('.messages-container');
	const documentToolbarContainer = document.querySelector(".wp-toolbar");

	showOptionsButton.addEventListener("click", toggleOptions);

	function toggleOptions(e) {
		topBar.classList.toggle("hidden");
	}

	window.addEventListener("scroll", scrollInfo);

	function scrollInfo(e) {


		if (document.querySelector(".wp-toolbar").scrollLeft > 100 || documentToolbarContainer.scrollTop > pluginTable.offsetTop) {

			if (!topBar.classList.contains("scrolled")) {
				topBar.classList.add("top-container-fixed", "scrolled", "hidden");
			} else {

				topBar.classList.add("top-container-fixed", "scrolled");

			}

		} else {
			// let elDistanceToTop = window.pageYOffset + pluginTable.getBoundingClientRect().top;
			topBar.classList.remove("top-container-fixed", "hidden", "scrolled");
			// showOptionsButton.classList.add("hidden");
		}
	}

	if (messageContainer) {
		setTimeout(() => {
			messageContainer.classList.add("hidden");
		}, 2000);
	}
}

window.addEventListener('DOMContentLoaded', (event) => {
	pluginHandler();
	selectMulTipleCheckboxesByShiftPressing();

});


function selectMulTipleCheckboxesByShiftPressing() {

	const checkboxes = document.querySelectorAll('.chkbox');
	let lastChecked = null;

	for (const chk of checkboxes) {
		chk.addEventListener("click", (e) => {

			const currIndexEl = [].indexOf.call(checkboxes, chk);
			const isChecked = e.currentTarget.checked;


			if (!lastChecked) {
				lastChecked = currIndexEl;
				return;
			}

			if (e.shiftKey) {

				let start = lastChecked;
				let end = currIndexEl;

				for (let i = start; i < end; i++) {
					checkboxes[i].checked = isChecked;
				}

			}

		});
	}
}

