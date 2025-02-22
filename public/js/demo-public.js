
'use strict';

document.addEventListener('DOMContentLoaded', function () {
	const options = document.querySelectorAll('.title');
	const contents = document.querySelectorAll('.content_unit');
	const titles = document.querySelectorAll('.content-title-name');


	options.forEach(function (option) {
		option.addEventListener('click', function () {
			const optionId = this.dataset.id;

			options.forEach(function (opt) {
				opt.classList.remove('clicked');
				opt.classList.add("unClicked");
			});

			contents.forEach(function (content) {
				const contentId = content.dataset.id;

				if (optionId === contentId) {
					content.style.display = 'flex';
				} else {
					content.style.display = 'none';
				}
			});

			titles.forEach(function (title) {
				const titleId = title.dataset.id;

				if (optionId === titleId) {
					title.style.display = 'block';
				} else {
					title.style.display = 'none';
				}
			});

			this.classList.remove('unClicked');
			this.classList.add('clicked');
		});
	});

	options[0].click();

	const searchInput = document.querySelector(".search-input")

	searchInput.addEventListener("keydown", (event) => {
		if (event.key === "Enter") {
			const query = event.target.value.toLowerCase()

			let result = []

			options.forEach(function (option) {

				if (option.innerText.toLowerCase().includes(query)) {
					result.push(option);
					option.style.display = "flex";
					console.log(option)
				} else {
					option.style.display = "none";
				}
			})

			result[0].click();
		}
	})


	// page type list logic

	const pageList = document.querySelector(".article_list_page")
	var numberOfPage = 1
	const unitOnEachPage = 5
	const listUnit = document.querySelectorAll(".article_list_page > li")

	const numberOfArticle = options.length

	const processPage = () => {
		listUnit.forEach((unit) => {
			const unitId = unit.dataset.id
			const articleNumber = numberOfArticle - unitId;
			let condition = (articleNumber <= (unitOnEachPage * numberOfPage)) && (articleNumber > ((numberOfPage - 1) * unitOnEachPage))

			if (!condition) {
				unit.style.display = "none"
			} else {
				unit.style.display = "flex"
			}
		})
	}

	processPage()

	const nextPage = document.querySelector(".next")
	const prePage = document.querySelector(".pre")
	const pos1 = document.querySelector(".pos1")
	const pos2 = document.querySelector(".pos2")
	const pos3 = document.querySelector(".pos3")

	const handleChangeOfPage = () => {
		if (numberOfPage === 1) {
			pos1.innerHTML = 1
			pos2.innerHTML = 2
			pos3.innerHTML = 3
		} else if (numberOfPage === numberOfArticle) {
			pos1.innerHTML = 83
			pos2.innerHTML = 84
			pos3.innerHTML = 85
		} else {
			pos2.innerHTML = numberOfPage
			pos1.innerHTML = numberOfPage - 1
			pos3.innerHTML = numberOfPage + 1
		}
	}
	handleChangeOfPage()

	nextPage.addEventListener("click", () => {
		numberOfPage == 85 ? numberOfPage = 85 : numberOfPage++
		processPage()
		handleChangeOfPage()
	})

	prePage.addEventListener("click", () => {
		numberOfPage === 1 ? numberOfPage = 1 : numberOfPage--
		processPage()
		handleChangeOfPage()
	})

});