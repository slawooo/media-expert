Create a minimal Angular frontend for my existing Symfony REST API.

An Angular project has already been created in the `frontend/` directory of this repository.
Please work within that existing Angular app and generate the minimal code needed there.

Important constraints:
- Keep the solution as simple as possible.
- This is a small recruitment task.
- I am not an Angular developer, so avoid advanced patterns.
- Do not use Angular Material, NgRx, external modal libraries, or unnecessary packages.
- Use plain CSS only.
- Keep the file structure small and practical.
- Prefer simple components and a simple service layer.
- If standalone components are the default, that is fine, but keep the code easy to understand.
- Do not generate enterprise architecture.

Backend API context:
- The backend already exists in this repository.
- All `/api/*` endpoints require HTTP Basic Auth.
- Use login: `api`
- Use password: `secret`
- The frontend should call the API through Angular proxy using `/api`.

Available endpoints:
- `GET /api/records`
- `GET /api/records/{id}`
- `PUT /api/records/{id}`
- `PATCH /api/records/{id}/status`

Record shape:
- `id: number`
- `number: string`
- `createdAt: string`
- `currentStatus: string`
- `statusHistory?: { id: number; status: string; createdAt: string }[]`

What I need:
1. A simple records list view.
2. Fetch records from the API.
3. Display them in a simple table or grid.
4. Add live search filtering on the frontend.
5. Add simple client-side pagination.
6. Add an Edit button for each row.
7. Open record editing in a very simple side panel (not a complex modal).
8. Allow editing the record number.
9. Allow changing the current status.
10. Save changes back to the API.
11. Refresh the list after successful update.

Please generate:
- TypeScript interfaces/models
- Angular service for API communication
- Main records list component
- Simple edit panel component
- Minimal CSS
- Proxy configuration for `/api`
- Any required configuration for HTTP Basic Auth in API calls

Also explain briefly:
- which files should be created or modified
- which commands I should run
- how to start the frontend
