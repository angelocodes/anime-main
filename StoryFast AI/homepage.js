        // FAQ Toggle Script
        const faqQuestions = document.querySelectorAll('.faq-question');

        faqQuestions.forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                question.classList.toggle('active');
                answer.classList.toggle('active');
            });
        });

        // Testimonial Carousel Script
        const testimonialIndicators = document.querySelectorAll('.testimonial-indicator');
        const testimonialContents = document.querySelectorAll('.testimonial-content');

        testimonialIndicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                testimonialIndicators.forEach(ind => ind.classList.remove('active'));
                testimonialContents.forEach(content => content.classList.remove('active'));
                indicator.classList.add('active');
                testimonialContents[index].classList.add('active');
            });
        });

        // Pricing Toggle Script
        const toggleButton = document.querySelector('.toggle-button');
        const pricingPrices = document.querySelectorAll('.pricing-price');
        const pricingDurations = document.querySelectorAll('.pricing-duration');

        toggleButton.addEventListener('click', () => {
            toggleButton.classList.toggle('active');
            pricingPrices.forEach(price => {
                if (toggleButton.classList.contains('active')) {
                    price.textContent = `$${(parseFloat(price.textContent.replace('$', '')) * 12 * 0.8).toFixed(2)}`;
                } else {
                    price.textContent = `$${(parseFloat(price.textContent.replace('$', '')) / 12 / 0.8).toFixed(2)}`;
                }
            });
            pricingDurations.forEach(duration => {
                if (toggleButton.classList.contains('active')) {
                    duration.textContent = 'per year';
                } else {
                    duration.textContent = 'per month';
                }
            });
        });