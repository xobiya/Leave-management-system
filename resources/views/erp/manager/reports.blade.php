<x-app-layout title="Reports" panel="manager">
    <div class="grid gap-6 erp-stagger">
        <div class="erp-card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="erp-label">Analytics</p>
                    <h2 class="mt-2 text-xl font-semibold">Leave trends</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <select class="erp-input">
                        <option>Last 6 months</option>
                        <option>Last 12 months</option>
                    </select>
                    <button class="erp-button">Export PDF</button>
                </div>
            </div>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="erp-card-muted p-4">
                    <p class="erp-label">Top request type</p>
                    <p class="mt-2 text-lg font-semibold">Annual Leave</p>
                    <p class="text-xs text-base-500">48% of all requests</p>
                </div>
                <div class="erp-card-muted p-4">
                    <p class="erp-label">Average duration</p>
                    <p class="mt-2 text-lg font-semibold">2.6 days</p>
                    <p class="text-xs text-base-500">Median across team</p>
                </div>
                <div class="erp-card-muted p-4">
                    <p class="erp-label">Approval speed</p>
                    <p class="mt-2 text-lg font-semibold">5.2 hrs</p>
                    <p class="text-xs text-base-500">Within target SLA</p>
                </div>
            </div>
        </div>

        <div class="erp-card p-6">
            <p class="erp-label">Detail report</p>
            <h3 class="mt-2 text-lg font-semibold">Department breakdown</h3>
            <table class="erp-table mt-4">
                <thead>
                    <tr>
                        <th>Team</th>
                        <th>Requests</th>
                        <th>Approved</th>
                        <th>Rejected</th>
                        <th>Avg. days</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Design</td>
                        <td>42</td>
                        <td>38</td>
                        <td>4</td>
                        <td>2.1</td>
                    </tr>
                    <tr>
                        <td>Engineering</td>
                        <td>55</td>
                        <td>50</td>
                        <td>5</td>
                        <td>2.8</td>
                    </tr>
                    <tr>
                        <td>Support</td>
                        <td>30</td>
                        <td>26</td>
                        <td>4</td>
                        <td>3.0</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
