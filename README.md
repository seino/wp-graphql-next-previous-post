# WPGraphql Next-Previous Post

This project is a fork of [m-inan/wp-graphql-next-previous-post](https://github.com/m-inan/wp-graphql-next-previous-post).

When using `wp-graphql`, you can use this package to bring the next and previous articles in the post, including those in the same category or taxonomy.

### Installation

```sh
cd wp-content/plugins

git clone --branch master https://github.com/seino/wp-graphql-next-previous-post.git
```

### Usage

#### Standard next/previous posts:

```graphql
query Post {
    post(id: 1, idType: DATABASE_ID) {
        title
        next {
            title
        }
        previous {
            title
        }
    }
}
```

#### Next/previous posts in the same category:

```graphql
query PostInCategory {
    post(id: 1, idType: DATABASE_ID) {
        title
        nextInCategory {
            title
        }
        previousInCategory {
            title
        }
    }
}
```

#### Next/previous posts in a custom taxonomy:

```graphql
query PostInTaxonomy {
    post(id: 1, idType: DATABASE_ID) {
        title
        nextInCategory(taxonomy: "custom_taxonomy") {
            title
        }
        previousInCategory(taxonomy: "custom_taxonomy") {
            title
        }
    }
}
```

### Dependencies

No Dependencies.

### Reporting Issues

If believe you've found an issue, please [report it](https://github.com/seino/wp-graphql-next-previous-post/issues) along with any relevant details to reproduce it.

### Asking for help

Please do not use the issue tracker for personal support requests. Instead, use StackOverflow.

### Contributions

Yes please! Feature requests / pull requests are welcome.