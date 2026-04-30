Great, I fixed one small configuration detail related to the API connection and now everything works correctly.
Now I would like you to add one small new feature:

Update the existing Angular frontend in the `frontend/` directory to support creating new records.

Keep the solution as simple as possible.
Do not redesign the app, do not add new libraries, and do not introduce unnecessary abstractions.

What I need:
- add an "Add record" button on the records list view
- when clicked, open the existing side panel used for editing
- in this case, the form should be empty
- allow entering `number` and `status` as plain text inputs, consistent with the current UI
- on save, call `POST /api/records`
- after successful creation, close the panel and refresh the list

Backend API:
- `POST /api/records`
- payload:
```json
{
  "number": "REC-001",
  "status": "new"
}
```

Notes:
- all API calls use HTTP Basic Auth with `api` / `secret`
- API calls should continue to go through `/api`
- reuse as much existing code as possible
- show clearly which files should be changed
- explain briefly what was added
