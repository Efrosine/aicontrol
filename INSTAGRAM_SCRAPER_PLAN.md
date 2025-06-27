# Instagram Scraper Integration Plan

## 1. Add ig-scraper Service to Docker Compose

-   Add `efrosine/ig-scraper` as a service in `docker-compose.yml`.
-   Expose port 5000.

## 2. Create Scraper Form and Controller

-   Create a Blade form for admin to input scraping parameters (accounts, suspected_account, post_count, comment_count).
-   Create a controller to:
    -   Save input parameters as JSON in `scraped_data.input_query`.
    -   Trigger HTTP POST to the ig-scraper container.
    -   Parse the `results` field from the JSON response and save it as a longtext in `scraped_results.data`.
    -   Link the `scraped_data` and `scraped_results` records in `scraped_data_results`.

## 3. Record Input and Output

-   Each HTTP call records all parameters in `scraped_data.input_query`.
-   The response's `results` field is stored in `scraped_results.data`.
-   The relationship is tracked in `scraped_data_results`.

## 4. Viewing Results

-   Admin and users can view scraped results via a results page.
-   Results are fetched from the `scraped_results` table.

## 5. Table Relationships

-   `scraped_data`: Stores input parameters.
-   `scraped_results`: Stores the parsed results.
-   `scraped_data_results`: Links the above two tables.

---

**Next Steps:**

-   Update `docker-compose.yml`.
-   Scaffold form and controller.
-   Implement logic for saving input, making HTTP request, saving response, and linking tables.
-   Create views for displaying results.
