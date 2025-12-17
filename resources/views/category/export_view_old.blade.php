<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories Export</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
        }

        .category {
            font-size: 14px;
            font-weight: bold;
        }

        .sub-category, .sub-sub-category, .section, .topic {
            font-size: 14px;
            color: #555;
        }

        .sub-category-header {
            font-size: 17px;
            color: #DA8028;
        }

        .sub-sub-category-header {
            font-size: 15px;
            color: #bd6af5;
        }

        .section-header {
            font-size: 17px;
            color: #9152ef;
        }

        .topic-header {
            font-size: 17px;
            color: #9152ef;
        }
    </style>
</head>
<body>
<table>
    <thead>
    <tr>
        <th>Category Id</th>
        <th>Category Name</th>
        <th colspan="4"></th>

        <th class="section-header">Section Id</th>
        <th class="section-header">Section Name</th>
        <th class="topic-header">Topic Id</th>
        <th class="topic-header">Topic Name</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($categories as $category)
        <tr>
            <td class="category">{{ $category->id }}</td>
            <td class="category">{{ $category->name }}</td>
            <td colspan="2"></td>
        </tr>
        @if(isset($category->subCategories) && $category->subCategories->count())
            <tr>
                <td colspan="2"></td>
                <td style="background-color: #bfefed;" class="sub-category-header">Sub Sub-Category Id</td>
                <td style="background-color: #bfefed;" class="sub-category-header">Sub Sub-Category Name</td>
                <td colspan="2"></td>
            </tr>
            @foreach($category->subCategories as $sub_category)
                <tr>
                    <td colspan="2"></td>
                    <td class="sub-category">{{ $sub_category->id }}</td>
                    <td class="sub-category">{{ $sub_category->name }}</td>
                    @if(isset($sub_category->subCategories) && $sub_category->subCategories->count() )
                        <td style="background-color: #bfefed;" class="sub-sub-category-header">Sub Sub-Category Id</td>
                        <td style="background-color: #bfefed;" class="sub-sub-category-header">Sub Sub-Category Name
                        </td>
                    @else
                        <td colspan="2"></td>
                    @endif
                </tr>
                @foreach($sub_category->subCategories as $sub_sub_category)

                    <tr>
                        <td colspan="4"></td>
                        <td class="sub-category">{{ $sub_sub_category->id }}</td>
                        <td class="sub-category">{{ $sub_sub_category->name }}</td>
                    </tr>
                @endforeach()
            @endforeach

        @endif

        @if(isset($category->exam_section) && $category->exam_section->count())
            @foreach($category->exam_section as $section)
                <tr>
                    <td colspan="6"></td>
                    <td class="section">{{ $section->id }}</td>
                    <td class="section">{{ $section->name }}</td>
                    <td colspan="2"></td>
                </tr>
            @endforeach
        @endif

        @if(isset($category->topics) && $category->topics->count())
            @foreach($category->topics as $topic)
                <tr>
                    <td colspan="8"></td>
                    <td class="topic">{{ $topic->id }}</td>
                    <td class="topic">{{ $topic->topic }}</td>
                </tr>
            @endforeach
        @endif
    @endforeach
    </tbody>
</table>
</body>
</html>
