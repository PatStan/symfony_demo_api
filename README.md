## Reflection and What I Could Improve

I think most of the decisions I took were the right choice. I like that I implemented caching in a ListProvider, so I hit the external API to retrieve the IDs for lists, then cache it for 5 minutes so I don't spam requests and hit limits/throttles on the API.
There is definitely room for improvement and expansion however:

Tests currently hit the real CRM API, which could lead to rate limiting or test pollution. With more time, I would implement mocking or use test doubles to isolate external calls. This would also be handy for unit testing the requests to the external API.

External API URLs are hard-coded. These should be moved to environment variables or injected via a service class for flexibility and security.

Business logic lives inside controller methods. Refactoring to dedicated service classes would improve readability, support SOLID principles, and make the code easier to test. Due to the time constraint I did not get around to doing this.

The task was estimated at 2-4 hours. I went slightly over this while getting familiar with Symfony, but wanted to demonstrate my ability to quickly adapt to a different tech stack. I'm very familiar with Laravel, but I chose to do it in Symfony as that is what your company uses, and I wanted to impress!

Validation could be improved. For example, the enquiry endpoint in EnquiryController does not currently check if the subscriber ULID exists in the database before submitting to the external API.

There's no Enquiry entity, which I would definitely implement in a real project. This could also then have a relation to the Subscriber Entity!

An OpenAPI spec was provided for the CRM API, but I didn’t use it directly. I found out you can auto-generate stubs and classes using a tool called OpenAPI generator. For this task, I focused on building the integration from scratch to better understand the flow. In the future, I'd definitely go with the tool I mentioned, which was completely new to me. This would help reduce boilerplate and ensure full schema alignment with the external API that was provided.
