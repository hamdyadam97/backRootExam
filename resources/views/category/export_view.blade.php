<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories Export</title>

</head>
<body>
<table style="width: 100%;border-collapse: collapse;font-family: Arial, sans-serif;">
    <thead>
    <tr>
        <th style=";background-color: #bfefed;color: #000000;border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 17px;">
            Category Id
        </th>
        <th style=";background-color: #bfefed;color: #000000;border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 17px;">
            Category Name
        </th>
        <th style=";background-color: #bfefed;color: #000000;border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 17px;"
            class="sub-category-header">Sub Category Id
        </th>
        <th style=";background-color: #bfefed;color: #000000;border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 17px;"
            class="sub-category-header">Sub Category Name
        </th>
        <th style=";background-color: #bfefed;color: #000000;border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 15px;"
            class="sub-sub-category-header">Sub Sub-Category Id
        </th>
        <th style=";background-color: #bfefed;color: #000000;border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 15px;"
            class="sub-sub-category-header">Sub Sub-Category Name
        </th>
        <th style=";background-color: #bfefed;color: #000000;border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 17px;"
            class="section-header">Section Id
        </th>
        <th style=";background-color: #bfefed;color: #000000;border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 17px;"
            class="section-header">Section Name
        </th>
        <th style=";background-color: #bfefed;color: #000000;border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 17px"
            class="topic-header">Topic Id
        </th>
        <th style=";background-color: #bfefed;color: #000000;border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 17px"
            class="topic-header">Topic Name
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach ($categories as $category)
        <tr>
            <td style="border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 14px;font-weight: bold;"
                class="category">{{ $category->id }}</td>
            <td style="border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 14px;font-weight: bold;"
                class="category">{{ $category->name }}</td>
            <td style="border: 1px solid #ccc;padding: 8px;text-align: center;" colspan="8"></td>
        </tr>
        @if(isset($category->subCategories) && $category->subCategories->count())
            @foreach($category->subCategories as $sub_category)
                @php
                    $maxRows = max(
                        $sub_category->subCategories->count(),
                        count($category->exam_section),
                        count($category->topics)
                    );
                @endphp
                <tr>
                    <td colspan="2"></td>
                    <td style="border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 14px;color: #555;"
                        class="sub-category">{{ $sub_category->id }}</td>
                    <td style="border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 14px;color: #555;"
                        class="sub-category">{{ $sub_category->name }}</td>
                    <td colspan="6"></td>
                </tr>
                @foreach($sub_category->subCategories as $sub_sub_category)
                    <tr>
                        <td colspan="4"></td>
                        <td style="border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 14px;color: #555;"
                            class="sub-sub-category">{{ $sub_sub_category->id }}</td>
                        <td style="border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 14px;color: #555;"
                            class="sub-sub-category">{{ $sub_sub_category->name }}</td>
                        <td colspan="4"></td>
                    </tr>
                @endforeach
            @endforeach
        @endif

        @if(isset($category->exam_section) && $category->exam_section->count())
            @foreach($category->exam_section as $section)
                <tr>
                    <td colspan="6"></td>
                    <td style="border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 14px;color: #555;"
                        class="section">{{ $section->id }}</td>
                    <td style="border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 14px;color: #555;"
                        class="section">{{ $section->name }}</td>
                    <td colspan="2"></td>
                </tr>
            @endforeach
        @endif

        @if(isset($category->topics) && $category->topics->count())
            @foreach($category->topics as $topic)
                <tr>
                    <td colspan="8"></td>
                    <td style="border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 14px;color: #555;"
                        class="topic">{{ $topic->id }}</td>
                    <td style="border: 1px solid #ccc;padding: 8px;text-align: center;font-size: 14px;color: #555;"
                        class="topic">{{ $topic->topic }}</td>
                </tr>
            @endforeach
        @endif
    @endforeach
    </tbody>
</table>
</body>
</html>
