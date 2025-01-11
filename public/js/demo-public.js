'use strict';

document.addEventListener('DOMContentLoaded', function () {
	const options = document.querySelectorAll('.title');
	const contents = document.querySelectorAll('.content_unit');
	const titles = document.querySelectorAll('.content-title-name');

	// Click event for options
	options.forEach(function (option) {
		option.addEventListener('click', function () {
			const optionId = this.dataset.id;

			// Update active styles for options
			options.forEach(opt => opt.classList.remove('clicked', 'unClicked'));
			this.classList.add('clicked');

			// Show/hide corresponding content and title
			contents.forEach(content => content.style.display = content.dataset.id === optionId ? 'flex' : 'none');
			titles.forEach(title => title.style.display = title.dataset.id === optionId ? 'block' : 'none');
		});
	});

	// Simulate click for the first option if available
	if (options.length > 0) {
		options[0].click();
	}

	// Search bar functionality
	const searchInput = document.querySelector(".search-input");
	searchInput.addEventListener("keydown", (event) => {
		if (event.key === "Enter") {
			const query = event.target.value.toLowerCase();
			let result = Array.from(options).filter(option => option.innerText.toLowerCase().includes(query));

			// Show matching options, hide others
			options.forEach(option => option.style.display = result.includes(option) ? 'flex' : 'none');

			// Simulate click for the first search result if any
			if (result.length > 0) {
				result[0].click();
			} else {
				console.log("No results found for query:", query);
			}
		}
	});

	// Pagination logic
	const pageList = document.querySelector(".article_list_page");
	const listUnit = document.querySelectorAll(".article_list_page > li");
	const numberOfArticles = options.length;
	const unitOnEachPage = 5;

	let currentPage = 1;
	const totalPages = Math.ceil(numberOfArticles / unitOnEachPage);

	const processPage = () => {
		listUnit.forEach((unit, index) => {
			let condition = index >= (currentPage - 1) * unitOnEachPage && index < currentPage * unitOnEachPage;
			unit.style.display = condition ? 'flex' : 'none';
		});
	};

	const updatePageIndicators = () => {
		const pos1 = document.querySelector(".pos1");
		const pos2 = document.querySelector(".pos2");
		const pos3 = document.querySelector(".pos3");

		pos1.textContent = currentPage > 1 ? currentPage - 1 : '';
		pos2.textContent = currentPage;
		pos3.textContent = currentPage < totalPages ? currentPage + 1 : '';
	};

	// Initial page setup
	processPage();
	updatePageIndicators();

	document.querySelector(".next").addEventListener("click", () => {
		if (currentPage < totalPages) {
			currentPage++;
			processPage();
			updatePageIndicators();
		}
	});

	document.querySelector(".pre").addEventListener("click", () => {
		if (currentPage > 1) {
			currentPage--;
			processPage();
			updatePageIndicators();
		}
	});
});
