<x-app-layout title="Team Availability" panel="manager">
    <div class="grid gap-6">
        <div class="erp-card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="erp-label">Coverage</p>
                    <h2 class="mt-2 text-xl font-semibold">Team availability view</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <select class="erp-input">
                        <option>All departments</option>
                        <option>Design</option>
                        <option>Engineering</option>
                    </select>
                    <button class="erp-button-ghost">Export</button>
                </div>
            </div>
            <table class="erp-table mt-6">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Role</th>
                        <th>Next leave</th>
                        <th>Coverage status</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>M. Alvarez</td>
                        <td>Product Designer</td>
                        <td>Feb 20 - Feb 21</td>
                        <td><span class="erp-badge erp-badge-warning">Limited</span></td>
                        <td>12.5 days</td>
                    </tr>
                    <tr>
                        <td>K. Singh</td>
                        <td>Frontend Engineer</td>
                        <td>Mar 2</td>
                        <td><span class="erp-badge erp-badge-success">Healthy</span></td>
                        <td>18 days</td>
                    </tr>
                    <tr>
                        <td>J. Okoye</td>
                        <td>Support Lead</td>
                        <td>Mar 4 - Mar 6</td>
                        <td><span class="erp-badge erp-badge-danger">Risk</span></td>
                        <td>9 days</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="erp-card p-5">
                <p class="erp-label">Coverage alert</p>
                <h3 class="mt-2 text-lg font-semibold">Support team</h3>
                <p class="mt-2 text-sm text-base-500">Two overlapping requests require backup staffing.</p>
                <button class="erp-button mt-4 w-full">Assign coverage</button>
            </div>
            <div class="erp-card p-5">
                <p class="erp-label">Plan ahead</p>
                <h3 class="mt-2 text-lg font-semibold">March capacity</h3>
                <p class="mt-2 text-sm text-base-500">Forecast suggests 12% drop in availability.</p>
                <button class="erp-button-ghost mt-4 w-full">Review forecast</button>
            </div>
            <div class="erp-card p-5">
                <p class="erp-label">Tools</p>
                <h3 class="mt-2 text-lg font-semibold">Bulk actions</h3>
                <p class="mt-2 text-sm text-base-500">Approve low-risk requests in one click.</p>
                <button class="erp-button-ghost mt-4 w-full">Open bulk tools</button>
            </div>
        </div>
    </div>
</x-app-layout>
