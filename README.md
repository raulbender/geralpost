# GeralPost 🌐⚓

GeralPost is an automated, high-performance news platform. It combines automated data aggregation with artificial intelligence to discover, curate, translate, and publish highly relevant international news, featuring an administrative "Human-in-the-Loop" workflow.

The core purpose of this project is to demonstrate modern backend architecture patterns, focusing on background processing, horizontal scalability, and clean code principles under the Laravel ecosystem.

---

## 🛠️ Key Architectural Features

* **Data-Driven Automation (Search Tasks):** Configurable target keywords managed directly through the database, allowing the system to dynamically adapt its scanning scope without altering source code.
* **RSS Feeds Deep Scraping:** Efficient ingestion of international RSS streams (like Google News global indexes) using Laravel's native HTTP Client.
* **Asynchronous Execution & Redis Queues:** Processing heavy operations (external HTTP calls, AI prompt evaluations) completely out of the request-response cycle using background Jobs powered by Redis.
* **Smart Content Curation (Two-Step LLM Prompting):** Leverages highly cost-effective models (like Gemini Flash-Lite) to run quick binary relevance filters, preventing database bloat and optimizing token costs before generating deep articles.
* **Clean & Scalable Architecture:** Clear separation of concerns by encapsulating third-party API communication inside isolated Service Layers, and enforcing data constraints via custom Eloquent Scopes (`scopeForFeed`, `scopeOnlyRevised`).
* **Human-In-The-Loop Validation:** An elegant, secure administrative dashboard where raw AI drafts wait for official review and approval via atomic RESTful operations (`PATCH`) protected against CSRF vulnerabilities.

---

## 🔄 System Architecture Flow

```text
[ Laravel Scheduler (15min Clock) ]
                │
                ▼ (Dispatches)
[ FetchAndProcessAiNews Background Job ] ──► Reads keywords from `search_tasks`
                │
                ▼ (Downloads)
[ Global RSS Feeds (XML parsing) ] ─────► Deduplication filter against local DB
                │
                ▼ (Step 1 AI Triagem)
[ Gemini Flash-Lite Filter ] ───────────► High-speed Geopolitics relevance check
                │
               ┌┴─────────────┐
            (YES)            (NO) ──► [ Discard & Log ]
                │
                ▼ (Step 2 AI Generation)
[ Deep Contextual Translation ] ────────► Generates parallel Title/Content (PT / EN)
                │
                ▼ (Persists)
[ Database: Status: 'pending' ] ────────► Appears exclusively in Admin Panel
                │
         [ Manual Approval ] ───────────► Status: 'published' -> Instant Feed UI
```

---

## 🧰 Tech Stack & Tools

* **Backend Framework:** PHP 8.2+ / Laravel (Strict Types enforced)
* **Database & Cache:** MySQL 8.0, Redis (Queue Driver)
* **Environment Containerization:** Docker / Laravel Sail
* **AI Integration:** Google Gemini API
* **Security & Quality:** Laravel Breeze Middleware, CSRF Protection, RESTful standard practices.